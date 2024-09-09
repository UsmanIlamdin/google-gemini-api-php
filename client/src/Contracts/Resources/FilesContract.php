<?php

namespace GeminiAPI\Contracts\Resources;

use GeminiAPI\Data\File;
use GeminiAPI\Responses\Files\ListFilesResponse;
use GuzzleHttp\Exception\GuzzleException;

interface FilesContract
{
    /**
     * Deletes the `File`.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.delete
     *
     * @param string $name The name of the `File` to delete
     *
     * @return bool If successful.
     *
     * @throws GuzzleException
     */
    public function delete(string $name): bool;

    /**
     * Gets the `File`.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.get
     *
     * @param string $name The name of the `File` to get
     *
     * @return File If successful.
     *
     * @throws GuzzleException
     */
    public function get(string $name): File;

    /**
     * Lists the metadata for `File`s owned by the requesting project.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.list
     *
     * @param array{
     *      pageSize?: int,
     *      pageToken?: string,
     * } $parameters
     *
     * @return ListFilesResponse If successful.
     *
     * @throws GuzzleException
     */
    public function list(array $parameters): ListFilesResponse;
}