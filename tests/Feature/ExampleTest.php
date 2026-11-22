<?php

test('the home page sends visitors on to the lobby', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('lobby'));
});
