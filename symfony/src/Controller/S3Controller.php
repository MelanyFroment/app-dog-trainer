<?php
namespace App\Controller;

use Aws\S3\S3Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class S3Controller extends AbstractController
{
    #[Route('/api/s3/presign', name: 's3_presign', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function getPresignedUrl(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $filename = $data['filename'] ?? null;
        $type = $data['filetype'] ?? 'image/jpeg';

        if (!$filename) {
            return new JsonResponse(['error' => 'Missing filename'], 400);
        }

        $bucket = $_ENV['AWS_S3_BUCKET'];
        $region = $_ENV['AWS_REGION'];

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $region,
            'credentials' => [
                'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
            ],
        ]);

        $key = 'uploads/' . uniqid() . '_' . $filename;

        $cmd = $s3->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $type,
        ]);

        $request = $s3->createPresignedRequest($cmd, '+10 minutes');

        return new JsonResponse([
            'url' => (string) $request->getUri(),
            'key' => $key,
        ]);
    }
}
