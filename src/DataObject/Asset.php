<?php

namespace Vector\DataObject;

use Vector\Kernel;
use Vector\Module\AbstractObject;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Exception;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class Asset extends AbstractObject
{

    protected FileSystemLogger $logger;

    /**
     * @var ?int $ID
     * Asset's ID, autogenerated on creation.
     */
    protected ?int $ID = null;

    /**
     * @var string $path
     * Asset's filepath, required.
     */
    protected string $path;

    /**
     * @var ?int $modifiedAt
     * Asset's modification date.
     */
    protected ?int $modifiedAt = null;

    /**
     * @var ?string $mimeType
     * Asset's mimetype.
     */
    protected ?string $mimeType = null;

    /**
     * @var ?int $size
     * Asset's file size.
     */
    protected ?int $size = null;

    /**
     * @var ?string $content
     * Asset's file content.
     */
    protected ?string $content = null;

    /**
     * @package Vector
     * __construct()
     */
    public function __construct(array $data = [])
    {
        $this->logger = new FileSystemLogger('storage');
        parent::__construct($data);
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getId()
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->ID;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getPath()
     * @return ?string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->setPath()
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = trim($path);
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getMimeType()
     * @return ?string
     */
    public function getMimeType(): ?string
    {
        if (null === $this->mimeType && 
            null !== ($filepath = $this->getFullpath())) {
                return false !== ($this->mimeType = mime_content_type($filepath)) ?
                    $this->mimeType :
                    null;
        }

        return $this->mimeType;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->setMimeType()
     * @param string $type
     * @return void
     */
    public function setMimeType(string $type): void
    {
        $this->mimeType = trim($type);
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getSize()
     * @return ?int 
     */
    public function getSize(): ?int
    {
        if (null === $this->size &&
            null !== ($filepath = $this->getFullpath())) {
                return false !== ($this->size = filesize($filepath)) ?
                    $this->size :
                    null;
        }

        return $this->size;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->setSize()
     * @param string $size
     * @return void
     */
    public function setSize(int $size): void
    {
        $this->size = trim($size);
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getMediaContent()
     * @return ?string 
     */
    public function getContent(): ?string
    {
        if (null === $this->content && 
            null !== ($filepath = $this->getFullpath()) && 
            false !== ($localHandle = fopen($filepath, 'r'))) {
                $this->content = fread($localHandle, filesize($filepath));
                return $this->content;
        }
        
        return $this->content;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->setContent()
     * @param string $content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = trim($content);
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getRoute()
     * @return string
     */
    public function getRoute(): string
    {
        return '/storage/' . $this->get('path');
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getStream()
     * @return mixed 
     */
    public function getStream(): mixed
    {
        if (null !== ($filepath = $this->getFullpath())) {
            return false !== ($localHandle = fopen($filepath, 'r')) ?
                $localHandle :
                null;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->getFullpath()
     * @return ?string 
     */
    protected function getFullpath(): ?string
    {
        $local = Kernel::getProjectRoot() . 'var/storage/' . $this->get('path');
        if (file_exists($local)) {
            return $local;
        }

        return null;
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->save()
     * @return void 
     */
    public function save(): void
    {

        if (null !== $this->getContent()) {
            try {
                file_put_contents(
                    Kernel::getProjectRoot() . 'var/storage/' . $this->getPath(),
                    $this->getContent()
                );
            } catch (Exception $e) {
                $this->logger->write($e);
                return;
            }

            $now = time();
            $query = "INSERT INTO `assets` (`ID`, `path`, `modifiedAt`, `mimeType`, `size`) 
                VALUES (:ID, :path, :modifiedAt, :mimeType, :size)
                ON DUPLICATE KEY UPDATE `path` = :path, 
                    `modifiedAt` = :modifiedAt, 
                    `mimeType` = :mimeType, 
                    `size` = :size";
            $q = $this->sql->prepare($query);

            $q->bindParam('ID', $this->ID, PDO::PARAM_INT);
            $q->bindParam('path', $this->path, PDO::PARAM_STR);
            $q->bindParam('modifiedAt', $now, PDO::PARAM_INT);

            $mime = $this->getMimeType();
            $q->bindParam('mimeType', $mime, PDO::PARAM_STR);

            $size = $this->getSize();
            $q->bindParam('size', $size, PDO::PARAM_INT);
            $q->execute();

            if (null !== ($id = $this->sql->lastInsertId())) {
                $this->ID = $id;
            }
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\Asset->delete()
     * @return void 
     */
    public function delete(): void
    {
        if (null !== $this->getId()) {
            $query = "DELETE FROM `assets` WHERE `ID` :id";
            $q = $this->sql->prepare($query);

            $q->bindParam('id', $this->ID, PDO::PARAM_INT);
            $q->execute();

            try {
                unlink(Kernel::getProjectRoot() . 'var/storage/' . $this->getPath());
            } catch (Exception $e) {
                $this->logger->write($e);
            }
        }
    }

}

