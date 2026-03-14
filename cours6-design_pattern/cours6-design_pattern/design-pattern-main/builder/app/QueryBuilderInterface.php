<?php

namespace App;

interface QueryBuilderInterface
{
	public function select(array $fields): self;

	public function from(string $table): self;

	public function where(string $condition): self;

	public function orderBy(string $field, string $direction = 'ASC'): self;

	public function limit(int $limit): self;

	public function getQuery(): string;

	public function reset(): self;
}