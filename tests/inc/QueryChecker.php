<?php declare(strict_types = 1);

namespace NextrasTests\Orm;


use Nette\Utils\FileSystem;
use Nextras\Dbal\Drivers\Exception\DriverException;
use Nextras\Dbal\ILogger;
use Nextras\Dbal\Result\Result;
use Tester\Assert;


class QueryChecker implements ILogger
{
	/** @var string */
	private $name;

	/** @var string */
	private $sqls = '';


	public function __construct(string $name)
	{
		$this->name = $name;
	}


	public function assert(): void
	{
		$file = __DIR__ . '/../sqls/' . $this->name . '.sql';
		if (!file_exists($file)) {
			FileSystem::createDir(dirname($file));
			FileSystem::write($file, $this->sqls);
		} else {
			Assert::same(FileSystem::read($file), $this->sqls);
		}
	}


	public function onConnect(): void
	{
	}


	public function onDisconnect(): void
	{
	}


	public function onQuery(string $sqlQuery, float $timeTaken, ?Result $result): void
	{
		if (strpos($sqlQuery, 'pg_catalog.') !== false) return;
		$this->sqls .= "$sqlQuery;\n";
	}


	public function onQueryException(string $sqlQuery, float $timeTaken, ?DriverException $exception): void
	{
	}
}
