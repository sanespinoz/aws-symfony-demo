<?php

namespace App\Service;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3Uploader
{
    private $s3;
    private $bucket;

    public function __construct(string $endpoint, string $region, string $key, string $secret, string $bucket)
    {
        $this->bucket = $bucket;

        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
        ]);
    }


    // 🔸 Para archivos reales (como en HomeController)
    public function uploadFile(UploadedFile $file): string
    {
        $key = $file->getClientOriginalName();

        $this->s3->putObject([
            'Bucket' => $this->bucket,
            'Key'    => $key,
            'Body'   => fopen($file->getPathname(), 'rb'),
        ]);

        return $key;
    }

    public function listFiles(): array
    {
        $result = $this->s3->listObjectsV2([
            'Bucket' => $this->bucket,
        ]);

        return array_map(
            fn($obj) => $obj['Key'],
            $result['Contents'] ?? []
        );
    }
}
