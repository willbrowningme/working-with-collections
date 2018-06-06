<?php
namespace App;

require_once './../vendor/autoload.php';

use App\GitHubScore;


function load_json($path){
    return json_decode(file_get_contents(__DIR__.'/'.$path), true);
}


// Lamps and Wallets Shopify Example - Sum all product variants
$products = load_json('products.json')['products'];

$price = collect($products)->filter(function ($item) {
    return collect(['Wallet', 'Lamp'])->contains($item['product_type']);
})->flatMap(function ($item) {
    return $item['variants'];
})->sum('price');


// GitHub Score Example
function githubScore($username)
{
    // Grab the events from the API, in the real world you'd probably use
    // Guzzle or similar here, but keeping it simple for the sake of brevity.
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: PHP'
            ]
        ]
    ];
    $context = stream_context_create($opts);
    $url = "https://api.github.com/users/{$username}/events";
    $events = collect(json_decode(file_get_contents($url, false, $context), true));

    // Using event types as lookup table with a default value of 1 if none found
    $score = $events->pluck('type')->map(function ($item) {
        return collect([
            'PushEvent' => 5,
            'CreateEvent' => 4,
            'IssuesEvent' => 3,
            'CommitCommentEvent' => 2,
        ])->get($item, 1);
    })->sum();

    return $score;
}

// githubScore('willbrowningme');

// using GitHubScore as a class
// $gitScore = GitHubScore::forUSer('willbrowningme');

