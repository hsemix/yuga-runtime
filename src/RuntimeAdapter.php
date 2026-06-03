<?php

namespace Yuga\Runtime;

interface RuntimeAdapter
{
    public function run(Kernel $kernel): void;
}