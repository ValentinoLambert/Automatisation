<?php

namespace App;

class MySQLQueryBuilder implements QueryBuilderInterface
{
	private array $select = ['*'];
	private string $from = '';
	private array $where = [];
	private ?string $orderBy = null;
	private ?int $limit = null;

	public function select(array $fields): QueryBuilderInterface
	{
		$this->select = empty($fields) ? ['*'] : $fields;

		return $this;
	}

	public function from(string $table): QueryBuilderInterface
	{
		$this->from = $table;

		return $this;
	}

	public function where(string $condition): QueryBuilderInterface
	{
		$this->where[] = $condition;

		return $this;
	}

	public function orderBy(string $field, string $direction = 'ASC'): QueryBuilderInterface
	{
		$this->orderBy = $field . ' ' . strtoupper($direction);

		return $this;
	}

	public function limit(int $limit): QueryBuilderInterface
	{
		$this->limit = $limit;

		return $this;
	}

	public function getQuery(): string
	{
		$query = 'SELECT ' . implode(', ', $this->select);
		$query .= ' FROM ' . $this->from;

		if (!empty($this->where)) {
			$query .= ' WHERE ' . implode(' AND ', $this->where);
		}

		if ($this->orderBy !== null) {
			$query .= ' ORDER BY ' . $this->orderBy;
		}

		if ($this->limit !== null) {
			$query .= ' LIMIT ' . $this->limit;
		}

		return $query . ';';
	}

	public function reset(): QueryBuilderInterface
	{
		$this->select = ['*'];
		$this->from = '';
		$this->where = [];
		$this->orderBy = null;
		$this->limit = null;

		return $this;
	}
}
