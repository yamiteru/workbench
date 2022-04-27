<?php
require __DIR__ . '/vendor/autoload.php';

class Event {
    private \Ds\Set $set;

    public function __construct(callable ...$subscriptions) {
        $this->set = new Ds\Set($subscriptions);
    }

    public function subscribe(callable $callback): callable {
        $this->set->add($callback);
        return fn() => $this->set->remove($callback);
    }

    public function publish(mixed $value): void {
        foreach ($this->set as $callback) $callback($value, $this);
    }

    public function size(): int {
        return $this->set->count();
    }

    public function has(callable $callback): bool {
        return $this->set->contains($callback);
    }

    public function clear(): void {
        $this->set->clear();
    }
}

class Once extends Event {
    public function __construct(callable ...$subscriptions) {
        parent::__construct(fn() => $this->clear(), ...$subscriptions);
    }
}