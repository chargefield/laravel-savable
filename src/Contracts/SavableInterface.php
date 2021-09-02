<?php

namespace Chargefield\Supermodel\Contracts;

interface SavableInterface
{
    public function makeSavable();
    public function savableColumns(): array;
    public function saveData();
}