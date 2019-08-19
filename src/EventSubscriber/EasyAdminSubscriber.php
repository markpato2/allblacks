<?php


namespace App\EventSubscriber;
use App\Service\FileService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Documento;


class EasyAdminSubscriber implements EventSubscriberInterface
{

    private $documentService;

    public function __construct(FileService $documentService)
    {
        $this->documentService = $documentService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'easy_admin.pre_persist' => array('postDocument'),
        );
    }

    function postDocument(GenericEvent $event) {
        $result = $event->getSubject();
        $method = $event->getArgument('request')->getMethod();

        if (! $result instanceof Documento || $method !== Request::METHOD_POST) {
            return;
        }







        if ($result->getdocumentoFilename() instanceof UploadedFile) {
            $url = $this->documentService->saveToDisk($result->getdocumentoFilename());
            $result->setdocumentoFilename($url);
        }
    }
}