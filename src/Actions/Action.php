<?php

namespace App\Actions;

abstract class Action {
    abstract public function execute(ActionInput $input): ActionOutput;
}