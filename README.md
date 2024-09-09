<p align="center">
    <img src="https://raw.githubusercontent.com/gemini-api-php/client/main/assets/example.png" width="800" alt="Gemini API PHP Client - Example">
</p>
<p align="center">
    <a href="https://packagist.org/packages/gemini-api-php/client"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/gemini-api-php/client"></a>
    <a href="https://packagist.org/packages/gemini-api-php/client"><img alt="Latest Version" src="https://img.shields.io/packagist/v/gemini-api-php/client"></a>
    <a href="https://packagist.org/packages/gemini-api-php/client"><img alt="License" src="https://img.shields.io/github/license/gemini-api-php/client"></a>
</p>

# Gemini API PHP Gemini

Gemini API PHP Client allows you to use the Google's generative AI models, like Gemini Pro and Gemini Pro Vision.

_This library is not developed or endorsed by Google._

- Nexius - **[github.com/erdemkose](https://github.com/erdemkose)**

## Table of Contents
- [Installation](#installation)
- [How to use](#how-to-use)
  - [Basic text generation](#basic-text-generation)
  - [Text generation using cached content and file uploaded](#text-generation-using-cached-content-and-file-uploaded)
  - [Multimodal input](#multimodal-input)
  - [Chat Session (Multi-Turn Conversations)](#chat-session-multi-turn-conversations)
  - [Chat Session with history](#chat-session-with-history)
  - [Streaming responses](#streaming-responses)
  - [Streaming Chat Session](#streaming-chat-session)
  - [Tokens counting](#tokens-counting)
  - [Cached Content Resource](#cached-content-resource)
    - [create](#create)
    - [delete](#delete)
    - [get](#get)
    - [list](#list)
    - [patch](#patch)
  - [File Resource](#file-resource)
    - [delete](#delete-5)
    - [get](#get-5)
    - [list](#list-5)
  - [Media Resource](#media-resource)
    - [upload](#upload)
  - [Listing models](#listing-models)
  - [Advanced Usages](#advanced-usages)
    - [Safety Settings and Generation Configuration](#safety-settings-and-generation-configuration)
    - [Using your own HTTP client](#using-your-own-http-client)
    - [Using your own HTTP client for streaming responses](#using-your-own-http-client-for-streaming-responses)

## Installation

> You need an API key to gain access to Google's Gemini API.
> Visit [Google AI Studio](https://makersuite.google.com/) to get an API key.

First step is to install the Gemini API PHP client with Composer.

```shell
composer require gemini-api-php/client
```

Gemini API PHP client does not come with an HTTP client.
If you are just testing or do not have an HTTP client library in your project,
you need to allow `php-http/discovery` composer plugin or install a PSR-18 compatible client library.

## How to use

### Basic text generation

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->geminiPro()->generateContent(
    new TextPart('PHP in less than 100 chars'),
);

print $response->text();
// PHP: A server-side scripting language used to create dynamic web applications.
// Easy to learn, widely used, and open-source.
```

### Text generation using cached content and file uploaded

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Resources\Parts\FilePart;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->geminiPro15Flash001()->generateContentWithCache(
    ["cachedContent" => "cachedContents/n9l42h1iszbd"],
    new TextPart("Give me the summary of the file uploaded")
);

print $response->text();
// PHP: This will give the summary of the file uploaded
```

### Multimodal input

> Image input modality is only enabled for Gemini Pro Vision model

```php
use GeminiAPI\Gemini;
use GeminiAPI\Enums\MimeType;
use GeminiAPI\Resources\Parts\ImagePart;
use GeminiAPI\Resources\Parts\TextPart;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->geminiProVision()->generateContent(
    new TextPart('Explain what is in the image'),
    new ImagePart(
        MimeType::IMAGE_JPEG,
        base64_encode(file_get_contents('elephpant.jpg')),
    ),
);

print $response->text();
// The image shows an elephant standing on the Earth.
// The elephant is made of metal and has a glowing symbol on its forehead.
// The Earth is surrounded by a network of glowing lines.
// The image is set against a starry background.
```

### Chat Session (Multi-Turn Conversations)

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;

$gemini = new Gemini('GEMINI_API_KEY');
$chat = $gemini->geminiPro()->startChat();

$response = $chat->sendMessage(new TextPart('Hello World in PHP'));
print $response->text();

$response = $chat->sendMessage(new TextPart('in Go'));
print $response->text();
```

```text
<?php
echo "Hello World!";
?>

This code will print "Hello World!" to the standard output.
```

```text
package main

import "fmt"

func main() {
    fmt.Println("Hello World!")
}

This code will print "Hello World!" to the standard output.
```

### Chat Session with history

```php
use GeminiAPI\Gemini;
use GeminiAPI\Enums\Role;
use GeminiAPI\Resources\Content;
use GeminiAPI\Resources\Parts\TextPart;

$history = [
    Content::text('Hello World in PHP', Role::User),
    Content::text(
        <<<TEXT
        <?php
        echo "Hello World!";
        ?>
        
        This code will print "Hello World!" to the standard output.
        TEXT,
        Role::Model,
    ),
];

$gemini = new Gemini('GEMINI_API_KEY');
$chat = $gemini->geminiPro()
    ->startChat()
    ->withHistory($history);

$response = $chat->sendMessage(new TextPart('in Go'));
print $response->text();
```

```text
package main

import "fmt"

func main() {
    fmt.Println("Hello World!")
}

This code will print "Hello World!" to the standard output.
```

### Streaming responses

> Requires `curl` extension to be enabled

In the streaming response, the callback function will be called whenever a response is returned from the server.

Long responses may be broken into separate responses, and you can start receiving responses faster using a content stream.

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Responses\GenerateContentResponse;

$callback = function (GenerateContentResponse $response): void {
    static $count = 0;

    print "\nResponse #{$count}\n";
    print $response->text();
    $count++;
};

$gemini = new Gemini('GEMINI_API_KEY');
$gemini->geminiPro()->generateContentStream(
    $callback,
    [new TextPart('PHP in less than 100 chars')],
);
// Response #0
// PHP: a versatile, general-purpose scripting language for web development, popular for
// Response #1
//  its simple syntax and rich library of functions.
```

### Streaming Chat Session

> Requires `curl` extension to be enabled 

```php
use GeminiAPI\Gemini;
use GeminiAPI\Enums\Role;
use GeminiAPI\Resources\Content;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Responses\GenerateContentResponse;

$history = [
    Content::text('Hello World in PHP', Role::User),
    Content::text(
        <<<TEXT
        <?php
        echo "Hello World!";
        ?>
        
        This code will print "Hello World!" to the standard output.
        TEXT,
        Role::Model,
    ),
];

$callback = function (GenerateContentResponse $response): void {
    static $count = 0;

    print "\nResponse #{$count}\n";
    print $response->text();
    $count++;
};

$gemini = new Gemini('GEMINI_API_KEY');
$chat = $gemini->geminiPro()
    ->startChat()
    ->withHistory($history);

$chat->sendMessageStream($callback, new TextPart('in Go'));
```

```text
Response #0
package main

import "fmt"

func main() {

Response #1
    fmt.Println("Hello World!")
}

This code will print "Hello World!" to the standard output.
```

### Embed Content

```php
use GeminiAPI\Gemini;
use GeminiAPI\Enums\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->embeddingModel(ModelName::Embedding)
    ->embedContent(
        new TextPart('PHP in less than 100 chars'),
    );

print_r($response->embedding->values);
// [
//    [0] => 0.041395925
//    [1] => -0.017692696
//    ...
// ]
```

## Cached Content Resource

### create
Creates CachedContent resource.

This example uses the _Sherlock Jr. movie_ video used in documentation. File was first uploaded with [media upload](#upload)

```php
use GeminiAPI\Data\CachedContent;
use GeminiAPI\Data\Content;

$file = 'https://generativelanguage.googleapis.com/v1beta/files/7j0qhgcmeeqh';

$response = $gemini->cachedContents()->create(
    new CachedContent(
        model: 'models/gemini-1.5-flash-001',
        displayName: 'sherlock jr movie',
        systemInstruction: Content::createTextContent("You are an expert video analyzer, and your job is to answer the user\'s query based on the video file you have access to."),
        contents: [
            Content::createFileContent($file, 'video/mp4', 'user'),
        ],
        ttl: '3600s',
    ),
);

$response->model; // models/gemini-1.5-flash-001
$response->name; // cachedContents/lg5adbi62ykx
$response->displayName; // sherlock jr movie
$response->createTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
$response->updateTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
$response->usageMetadata->totalTokenCount; // 
$response->expireTime->format('Y-m-d H:i:s'); // 2024-07-05 00:15:53

$response->toArray(); // ['model' => 'models/gemini-1.5-flash-001', ...]
```

<details>
<summary>Generate Content with a Cached Content</summary>

The timeout for `generateContent` is set to 60s, request timeout to 120s. If your request is still exceeding these limits you may propose an increase.

```php
use GeminiAPI\Data\Content;

$response = $gemini->geminiPro15Flash001()->generateContentWithCache(
            ["cachedContent" => "cachedContents/n9l42h1iszbd"],
            new TextPart("Give me the summary of the file uploaded")
        );

echo $response->text();
```
</details>

### delete
Deletes CachedContent resource.

```php
$response = $gemini->cachedContents()->delete('cachedContents/2wojeqz7srpu');

if ($response === true) {
    echo 'Successfully deleted the cached Content';
}
```

### get
Reads CachedContent resource.

```php
$response = $gemini->cachedContents()->get('cachedContents/2wojeqz7srpu');

$response->model; // models/gemini-1.5-flash-001
$response->name; // cachedContents/2wojeqz7srpu
$response->displayName; // Repository Specialist
$response->createTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
$response->updateTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
$response->usageMetadata->totalTokenCount; // 259246
$response->expireTime->format('Y-m-d H:i:s'); // 2024-08-04 23:15:53

$response->toArray(); // ['model' => 'models/gemini-1.5-flash-001', ...]

```

### list
Lists CachedContents.

```php
$response = $gemini->cachedContents()->list();

$response->nextPageToken; // 

foreach ($response->cachedContents as $cachedContent) {
    $cachedContent->model; // models/gemini-1.5-flash-001
    $cachedContent->name; // cachedContents/2wojeqz7srpu
    $cachedContent->displayName; // Repository Specialist
    $cachedContent->createTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
    $cachedContent->updateTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
    $cachedContent->usageMetadata->totalTokenCount; // 259246
    $cachedContent->expireTime->format('Y-m-d H:i:s'); // 2024-10-28 17:02:31
}

$response->toArray(); // ['cachedContents' => [...], ...]
```

### patch
Updates CachedContent resource (only expiration is updatable).

```php
$response = $gemini->cachedContents()->patch([
    'name' => 'cachedContents/2wojeqz7srpu',
    'updateMask' => 'ttl',
    'cachedContent' => new CachedContent(
        ttl: '3600s'
    ),
]);

$response->model; // models/gemini-1.5-flash-001
$response->name; // cachedContents/2wojeqz7srpu
$response->displayName; // Repository Specialist
$response->createTime->format('Y-m-d H:i:s'); // 2024-07-04 23:15:53
$response->updateTime->format('Y-m-d H:i:s'); // 2024-07-05 07:02:21
$response->usageMetadata->totalTokenCount; // 259246
$response->expireTime->format('Y-m-d H:i:s'); // 2024-07-05 08:02:21

$response->toArray(); // ['model' => 'models/gemini-1.5-flash-001', ...]
```

## File Resource

### delete
Deletes the `File`.

```php
$response = $gemini->files()->delete('files/qrbxtbaehccw');
if ($response === true) {
    echo 'File deleted successfully';
}
```

### get
Gets the metadata for the given `File`.

```php
$response = $gemini->files()->get('files/m8uuuytf6niz');

$response->name; // files/m8uuuytf6niz
$response->displayName; // Sample File 2
$response->mimeType; // image/jpeg
$response->sizeBytes; // 44485
$response->createTime->format('Y-m-d H:i:s'); // 2024-07-08 20:51:46
$response->updateTime->format('Y-m-d H:i:s'); // 2024-07-08 20:51:46
$response->expirationTime->format('Y-m-d H:i:s'); // 2024-07-10 20:51:46
$response->sha256Hash; // TZhZGZiMDUzMzM...
$response->uri; // https://generativelanguage.googleapis.com/v1beta/files/m8uuuytf6niz
$response->state->value; // ACTIVE

$response->toArray(); // ['name' => 'files/m8uuuytf6niz', ...]
```

### list
Lists the metadata for `File`s owned by the requesting project.

```php
$response = $gemini->files()->list();

$response->nextPageToken; // 

foreach ($response->files as $file) {
    $file->name; // files/qrbxtbaehccw
    $file->displayName; // Sample File
    $file->mimeType; // image/png
    $file->sizeBytes; // 357556
    $file->createTime->format('Y-m-d H:i:s'); // 2024-07-08 20:26:59
    $file->updateTime->format('Y-m-d H:i:s'); // 2024-07-08 20:26:59
    $file->expirationTime->format('Y-m-d H:i:s'); // 2024-07-10 20:26:59
    $file->sha256Hash; // NmI4NmM3M...
    $file->uri; // https://generativelanguage.googleapis.com/v1beta/files/qrbxtbaehccw
    $file->state->value; // ACTIVE
}

$response->toArray(); // ['files' => [...]]
```

## Media Resource

### upload
Creates a `File`.

```php
use GeminiAPI\Data\File;

# Example uses video downloaded from
# https://storage.googleapis.com/generativeai-downloads/data/Sherlock_Jr_FullMovie.mp4

$metaData = new File(
    displayName: 'Demo File'
);

# The file was excluded from commit due to impact it'd have on cloning this repo,
# download and change path, or can upload any file of choice
$response = $gemini->media()->upload(__DIR__ .'/files/Sherlock_Jr_FullMovie.mp4', $metaData);

$file = $response->file;
$file->name; // files/7j0qhgcmeeqh
$file->displayName; // Sherlock Jr. video
$file->mimeType; // video/mp4
$file->sizeBytes; // 331623233
$file->createTime->format('Y-m-d H:i:s'); // 2024-07-27 22:31:25
$file->updateTime->format('Y-m-d H:i:s'); // 2024-07-27 22:31:25
$file->expirationTime->format('Y-m-d H:i:s'); // 2024-07-29 22:31:25
$file->sha256Hash; // ZjAwNGM2ZjJiMzNlNjYxYzYwOTU1MzU3MDliYzUzMjY4ZDUzMjNlYzdhNTdlOGJjNGFlOTczNjJlZDM0MWI1Yg==
$file->uri; // https://generativelanguage.googleapis.com/v1beta/files/7j0qhgcmeeqh
$file->state->value; // PROCESSING

$response->toArray(); // ['file' => [...]]
```

### Tokens counting

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->geminiPro()->countTokens(
    new TextPart('PHP in less than 100 chars'),
);

print $response->totalTokens;
// 10
```

### Listing models

```php
use GeminiAPI\Gemini;

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->listModels();

print_r($response->models);
//[
//  [0] => GeminiAPI\Resources\Model Object
//    (
//      [name] => models/gemini-pro
//      [displayName] => Gemini Pro
//      [description] => The best model for scaling across a wide range of tasks
//      ...
//    )
//  [1] => GeminiAPI\Resources\Model Object
//    (
//      [name] => models/gemini-pro-vision
//      [displayName] => Gemini Pro Vision
//      [description] => The best image understanding model to handle a broad range of applications
//      ...
//    )
//]
```

### Advanced Usages

#### Safety Settings and Generation Configuration

```php
use GeminiAPI\Gemini;
use GeminiAPI\Enums\HarmCategory;
use GeminiAPI\Enums\HarmBlockThreshold;
use GeminiAPI\GenerationConfig;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\SafetySetting;

$safetySetting = new SafetySetting(
    HarmCategory::HARM_CATEGORY_HATE_SPEECH,
    HarmBlockThreshold::BLOCK_LOW_AND_ABOVE,
);
$generationConfig = (new GenerationConfig())
    ->withCandidateCount(1)
    ->withMaxOutputTokens(40)
    ->withTemperature(0.5)
    ->withTopK(40)
    ->withTopP(0.6)
    ->withStopSequences(['STOP']);

$gemini = new Gemini('GEMINI_API_KEY');
$response = $gemini->geminiPro()
    ->withAddedSafetySetting($safetySetting)
    ->withGenerationConfig($generationConfig)
    ->generateContent(
        new TextPart('PHP in less than 100 chars')
    );
```

#### Using your own HTTP client

```php
use GeminiAPI\Gemini as Gemini;
use GeminiAPI\Resources\Parts\TextPart;
use GuzzleHttp\Client as GuzzleClient;

$guzzle = new GuzzleClient([
  'proxy' => 'http://localhost:8125',
]);

$gemini = new Gemini('GEMINI_API_KEY', $guzzle);
$response = $gemini->geminiPro()->generateContent(
    new TextPart('PHP in less than 100 chars')
);
```

#### Using your own HTTP client for streaming responses

> Requires `curl` extension to be enabled

Since streaming responses are fetched using `curl` extension, they cannot use the custom HTTP client passed to the Gemini Client.
You need to pass a `CurlHandler` if you want to override connection options.

The following curl options will be overwritten by the Gemini.

- `CURLOPT_URL`
- `CURLOPT_POST`
- `CURLOPT_POSTFIELDS`
- `CURLOPT_WRITEFUNCTION`

You can also pass the headers you want to be used in the requests.

```php
use GeminiAPI\Gemini;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Responses\GenerateContentResponse;

$callback = function (GenerateContentResponse $response): void {
    print $response->text();
};

$ch = curl_init();
curl_setopt($ch, \CURLOPT_PROXY, 'http://localhost:8125');

$gemini = new Gemini('GEMINI_API_KEY');
$gemini->withRequestHeaders([
        'User-Agent' => 'My Gemini-backed app'
    ])
    ->geminiPro()
    ->generateContentStream(
        $callback,
        [new TextPart('PHP in less than 100 chars')],
        $ch,
    );
```
