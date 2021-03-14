<?php

namespace Pulunomoe\DataMapper;

use PDO;
use Pulunomoe\DataMapper\Attribute\Entity;
use Pulunomoe\DataMapper\Attribute\Property;
use Pulunomoe\DataMapper\Attribute\Validator;
use ReflectionClass;

class DataMapper
{
	private PDO $pdo;
	private string $class;
	private string $table;
	private array $columns;

	////////////////////////////////////////////////////////////////////////////

	public function __construct(PDO $pdo, string $class)
	{
		$this->pdo = $pdo;
		$this->class = $class;

		$r = new ReflectionClass($class);

		$attrs = $r->getAttributes(Entity::class);
		if (empty($attrs[0])) {
			throw new ValidationException('Invalid entity class');
		}

		$this->table = $attrs[0]->newInstance()->getTable();

		$props = $r->getProperties();
		foreach ($props as $prop) {

			$attrs = $prop->getAttributes(Property::class);
			if (!empty($attrs[0])) {
				$attrs = $attrs[0]->newInstance();
				$this->columns[$prop->name]['column'] = $attrs->getColumn();
			}

			$attrs = $prop->getAttributes(Validator::class);
			if (!empty($attrs[0])) {
				$attrs = $attrs[0]->newInstance();
				$this->columns[$prop->name]['validators'] = $attrs->getValidators();
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////

	public function findAll(string $orderBy = '', bool $desc = false, int $limit = 10, int $offset = 0): array
	{
		$sql = $this->getSelectSql();
		$sql .= $this->getOrderBySql($orderBy, $desc);
		$sql .= $this->getLimitSql($limit, $offset);

		$rows = $this->pdo->query($sql)->fetchAll();

		$objects = [];
		foreach ($rows as $row) {
			$objects[$row[$this->columns['id']['column']]] = $this->mapRowToObject($row);
		}

		return $objects;
	}

	public function findAllBy(string $column, string $value, string $orderBy = '', bool $desc = false, int $limit = 10, int $offset = 0): array
	{
		$sql = $this->getSelectSql();
		$sql .= ' WHERE ';
		$sql .= $this->columns[$column]['column'];
		$sql .= ' = :' . $column;
		$sql .= $this->getOrderBySql($orderBy, $desc);

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':'.$column => $value]);
		$rows = $stmt->fetchAll();

		$objects = [];
		foreach ($rows as $row) {
			$objects[$row[$this->columns['id']['column']]] = $this->mapRowToObject($row);
		}

		return $objects;
	}

	public function findOne(int $id): ?EntityClass
	{
		$sql = $this->getSelectSql();
		$sql .= ' WHERE ';
		$sql .= $this->columns['id']['column'];
		$sql .= ' = :id';

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':id' => $id]);
		$row = $stmt->fetch();

		return empty($row) ? null : $this->mapRowToObject($row);
	}

	public function create(EntityClass $object): EntityClass
	{
		$this->validateForCreate($object);

		$sql = $this->getInsertSql();

		$stmt = $this->pdo->prepare($sql);
		foreach (array_keys($this->columns) as $column) {
			if ($column == 'id') continue;
			$stmt->bindValue(':' . $column, $object->$column);
		}
		$stmt->execute();

		$object->id = $this->pdo->lastInsertId();

		return $object;
	}

	public function update(EntityClass $object): EntityClass
	{
		$this->validateForUpdate($object);

		$sql = $this->getUpdateSql();

		$stmt = $this->pdo->prepare($sql);
		foreach (array_keys($this->columns) as $column) {
			$stmt->bindValue(':' . $column, $object->$column);
		}
		$stmt->execute();

		return $object;
	}

	public function delete(int $id): void
	{
		$sql = $this->getDeleteSql();

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':id' => $id]);
	}

	////////////////////////////////////////////////////////////////////////////

	private function validateForCreate(EntityClass $object): void
	{
		$errors = [];

		foreach ($this->columns as $key => $column) {
			if (!empty($column['validators'])) {
				foreach ($column['validators'] as $validator) {
					$result = $validator::validateForCreate($this->pdo, $object->$key);
					if (!empty($result)) {
						$errors[] = $key . ' ' . $result;
					}
				}
			}
		}

		if (!empty($errors)) {
			throw new ValidationException($errors);
		}
	}

	private function validateForUpdate(EntityClass $object): void
	{
		$errors = [];

		foreach ($this->columns as $key => $column) {
			if (!empty($column['validators'])) {
				foreach ($column['validators'] as $validator) {
					$result = $validator::validateForUpdate($this->pdo, $object->$key);
					if (!empty($result)) {
						$errors[] = $key . ' ' . $result;
					}
				}
			}
		}

		if (!empty($errors)) {
			throw new ValidationException($errors);
		}
	}

	////////////////////////////////////////////////////////////////////////////

	private function getSelectSql(): string
	{
		$sql = 'SELECT ';

		foreach ($this->columns as $column) {
			$sql .= $column['column'] . ', ';
		}
		$sql = rtrim($sql, ', ');

		$sql .= ' FROM ';
		$sql .= $this->table;

		return $sql;
	}

	private function getOrderBySql(string $orderBy, bool $desc): string
	{
		$sql = ' ORDER BY ';
		$sql .= empty($orderBy) ? $this->columns['id']['column'] : $this->columns[$orderBy]['column'];
		$sql .= $desc ? ' DESC' : ' ASC';

		return $sql;
	}

	public function getLimitSql(int $limit, int $offset): string
	{
		$sql = ' LIMIT ';
		$sql .= $limit;
		$sql .= ' OFFSET ';
		$sql .= $offset;

		return $sql;
	}

	private function getInsertSql(): string
	{
		$sql = 'INSERT INTO ';
		$sql .= $this->table;
		$sql .= ' (';

		foreach ($this->columns as $objectPropertyName => $dbFieldName) {
			if ($objectPropertyName == 'id') continue;
			$sql .= $dbFieldName['column'] . ', ';
		}
		$sql = rtrim($sql, ', ');

		$sql .= ' ) VALUES (';

		foreach (array_keys($this->columns) as $column) {
			if ($column == 'id') continue;
			$sql .= ':' . $column . ', ';
		}
		$sql = rtrim($sql, ', ');

		$sql .= ')';

		return $sql;
	}

	private function getUpdateSql(): string
	{
		$sql = 'UPDATE ';
		$sql .= $this->table;
		$sql .= ' SET ';

		foreach ($this->columns as $objectPropertyName => $dbFieldName) {
			if ($objectPropertyName == 'id') continue;
			$sql .= $dbFieldName['column'] . ' = :' . $objectPropertyName . ', ';
		}
		$sql = rtrim($sql, ', ');

		$sql .= ' WHERE ';
		$sql .= $this->columns['id']['column'];
		$sql .= ' = :id';

		return $sql;
	}

	private function getDeleteSql(): string
	{
		$sql = 'DELETE FROM ';
		$sql .= $this->table;
		$sql .= ' WHERE ';
		$sql .= $this->columns['id']['column'];
		$sql .= ' = :id';

		return $sql;
	}

	////////////////////////////////////////////////////////////////////////////

	private function mapRowToObject(array $row): EntityClass
	{
		$object = new $this->class();
		foreach ($this->columns as $objectPropertyName => $dbFieldName) {
			$object->$objectPropertyName = $row[$dbFieldName['column']];
		}

		return $object;
	}
}