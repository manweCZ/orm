<?php declare(strict_types = 1);

/**
 * @testCase
 * @dataProvider ../../../databases.ini
 */

namespace NextrasTests\Orm\Integration\Entity;


use NextrasTests\Orm\Author;
use NextrasTests\Orm\Book;
use NextrasTests\Orm\DataTestCase;
use NextrasTests\Orm\Publisher;
use NextrasTests\Orm\Tag;
use Tester\Assert;


require_once __DIR__ . '/../../../bootstrap.php';


class EntityCloningTest extends DataTestCase
{

	public function testCloningOneHasMany(): void
	{
		/** @var Book $book */
		$book = $this->orm->books->getByIdChecked(1);

		$newBook = clone $book;

		Assert::same($book->author, $newBook->author);
		Assert::same(2, $newBook->tags->count());

		Assert::false($newBook->isPersisted());
		Assert::true($newBook->isModified());

		$this->orm->books->persistAndFlush($newBook);

		Assert::true($newBook->isPersisted());
		Assert::false($newBook->isModified());
		Assert::same(2, $newBook->tags->countStored());
	}


	public function testCloningManyHasMany(): void
	{
		$author = $this->e(Author::class, ['name' => 'New Author']);
		$publisher = $this->e(Publisher::class, ['name' => 'Publisher']);
		$book = $this->e(Book::class, ['author' => $author, 'title' => 'New Book', 'publisher' => $publisher]);
		$tag1 = $this->e(Tag::class, ['name' => 'Tag 1']);
		$tag2 = $this->e(Tag::class, ['name' => 'Tag 2']);
		$tag3 = $this->e(Tag::class, ['name' => 'Tag 3']);

		$book->tags->set([$tag1, $tag2, $tag3]);
		$this->orm->books->persistAndFlush($book);

		$newBook = clone $book;

		Assert::same($book->author, $newBook->author);
		Assert::same(3, $newBook->tags->count());
		Assert::same([$tag1, $tag2, $tag3], iterator_to_array($newBook->tags));

		$book->author = $this->e(Author::class, ['name' => 'New Author 2']);
		$book->tags->set([$tag1, $tag2]);

		Assert::same($author, $newBook->author);
		Assert::same([$tag1, $tag2, $tag3], iterator_to_array($newBook->tags));
	}

}


$test = new EntityCloningTest();
$test->run();
