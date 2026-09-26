<?php

// Temporary probe — written and deleted within the same session. Confirms the test
// environment actually targets sqlite :memory: (per phpunit.xml) and not the real
// mysql dev database, before running anything else that uses RefreshDatabase.
it('uses the sqlite in-memory database during tests, never the real mysql dev database', function () {
    expect(config('database.default'))->toBe('sqlite');
    expect(config('database.connections.sqlite.database'))->toBe(':memory:');
});
