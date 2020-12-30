<?php

namespace Pulunomoe\DataMapper\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Pulunomoe\DataMapper\DataMapper;

class DataMapperTest extends TestCase
{
	private static PDO $pdo;

	public static function setUpBeforeClass(): void
	{
		self::$pdo = new PDO('sqlite::memory:');
		self::$pdo->exec('CREATE TABLE tests (id_field INTEGER PRIMARY KEY, name_field TEXT NOT NULL, description_field TEXT NULL)');
	}

	protected function setUp(): void
	{
		self::$pdo->exec('DELETE FROM tests');
	}

	public function testFindAll()
	{
		// GIVEN I have several entities stored in the database
		$entities = [
			['name' => 'aiam', 'description' => 'wow aiam'],
			['name' => 'bebex', 'description' => 'wow bebex'],
			['name' => 'cicac', 'description' => 'wow cicac']
		];

		$expectedEntities = [];
		foreach ($entities as $entity) {
			$entity = $this->saveEntity($entity);
			$expectedEntities[$entity['id']] = $entity;
		}

		// WHEN I call the findAll method
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$actualEntities = $dm->findAll();

		// THEN I should be able to see the entities
		foreach ($actualEntities as $actual) {
			$expected = $expectedEntities[$actual->id];
			$this->assertEqualsCanonicalizing($expected, json_decode('' . $actual, true));
		}
	}

	public function testFindSome()
	{
		// GIVEN I have a lot of entities stored in the database
		$expectedEntities = [];
		for ($i = 0; $i < 50; $i++) {
			$entity = $this->saveEntity(['name' => 'test'.$i, 'description' => 'wow test'.$i]);
			$expectedEntities[$entity['id']] = $entity;
		}

		// WHEN I call the findAll method specifying the limit and offset
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$actualEntities = $dm->findAll(limit: 10, offset: 10);

		// THEN I should be able to see some of the entities
		$this->assertEquals(10, sizeof($actualEntities));
		foreach ($actualEntities as $actual) {
			$expected = $expectedEntities[$actual->id];
			$this->assertEqualsCanonicalizing($expected, json_decode('' . $actual, true));
		}
	}

	public function testFindAllBy()
	{
		// GIVEN I have several entities stored in the database
		$entities = [
			['name' => 'aiam', 'description' => 'wow aiam'],
			['name' => 'bebex', 'description' => 'wow bebex'],
			['name' => 'cicac', 'description' => 'wow cicac']
		];

		$expectedEntities = [];
		foreach ($entities as $entity) {
			$entity = $this->saveEntity($entity);
			$expectedEntities[$entity['id']] = $entity;
		}

		// WHEN I call the testFindAllBy method specifying the criteria
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$actualEntities = $dm->findAllBy('name', 'aiam');

		// THEN I should be able to see the matching entities
		$this->assertEquals(1, sizeof($actualEntities));
		foreach ($actualEntities as $actual) {
			$expected = $expectedEntities[$actual->id];
			$this->assertEqualsCanonicalizing($expected, json_decode('' . $actual, true));
		}
	}

	public function testFindOne()
	{
		// GIVEN I have an entity stored in the database
		$expectedEntity = $this->saveEntity(['name' => 'aiam', 'description' => 'wow aiam']);

		// WHEN I call the findOne method with the entity id
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$actualEntity = $dm->findOne(1);

		// THEN I should be able to see the entities
		$this->assertEqualsCanonicalizing($expectedEntity, json_decode('' . $actualEntity, true));
	}

	public function testCreate()
	{
		// GIVEN I have an entity I want to store in the database
		$entity = ['name' => 'aiam', 'description' => 'wow aiam'];

		// WHEN I call the create method with the entity data
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$expectedEntity = new TestEntity();
		$expectedEntity->name = $entity['name'];
		$expectedEntity->description = $entity['description'];
		$expectedEntity = $dm->create($expectedEntity);

		// THEN the entity should be stored in the database
		$actualEntity = $dm->findOne($expectedEntity->id);
		$this->assertEqualsCanonicalizing(json_decode('' . $expectedEntity, true), json_decode('' . $actualEntity, true));
	}

	public function testUpdate()
	{
		// GIVEN I have an entity stored in the database
		$oldEntity = $this->saveEntity(['name' => 'aiam', 'description' => 'wow aiam']);
		// AND I want to update the entity
		$newEntity = ['name' => 'bebex', 'description' => 'wow bebex'];

		// WHEN I call the update method with the new entity data
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$expectedEntity = new TestEntity();
		$expectedEntity->id = $oldEntity['id'];
		$expectedEntity->name = $newEntity['name'];
		$expectedEntity->description = $newEntity['description'];
		$expectedEntity = $dm->update($expectedEntity);

		// THEN the entity should be updated in the database
		$actualEntity = $dm->findOne($expectedEntity->id);
		$this->assertEqualsCanonicalizing(json_decode('' . $expectedEntity, true), json_decode('' . $actualEntity, true));
	}

	public function testDelete()
	{
		// GIVEN I have an entity stored in the database
		$entity = $this->saveEntity(['name' => 'aiam', 'description' => 'wow aiam']);

		// WHEN I call the delete method with the entity id
		$dm = new DataMapper(self::$pdo, TestEntity::class);
		$dm->delete($entity['id']);

		// THEN the entity should be deleted in the database
		$actualEntity = $dm->findOne($entity['id']);
		$this->assertNull($actualEntity);
	}

	private function saveEntity(array $entity): array
	{
		$stmt = self::$pdo->prepare('INSERT INTO tests (name_field, description_field) VALUES (:name, :description)');
		$stmt->execute([
			'name' => $entity['name'],
			'description' => $entity['description']
		]);
		$entity['id'] = self::$pdo->lastInsertId();

		return $entity;
	}
}