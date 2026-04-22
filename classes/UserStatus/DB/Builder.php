<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\UserStatus\DB\Element;

use Leifos\VedaConnector\I\UserStatus\DB\BuilderInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\FactoryInterface as UserDBElementFactoryInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\HandlerInterface as UserDBElementInterface;
use Leifos\VedaConnector\I\Logger\HandlerInterface as LoggerInterface;
use Leifos\VedaConnector\I\UserStatus\DB\Element\Status;
use Leifos\VedaConnector\I\UserStatus\DB\HandlerInterface as UserDBInterface;

class Builder implements BuilderInterface
{
    protected UserDBElementInterface $element;

    public function __construct(
        protected UserDBElementFactoryInterface $user_element_factory,
        protected UserDBInterface $user_db,
        protected LoggerInterface $logger
    ) {
        $this->element = $this->user_element_factory->handler();
    }

    public function withOID(
        string $oid,
        bool $load_from_db = true
    ) : BuilderInterface {
        $new_builder = clone $this;
        $this->logger->debug($load_from_db
            ? 'Looking for existing veda user with oid: ' . $oid
            : 'Skip looking for an existing veda user with oid: ' . $oid
        );
        $existing_element = $load_from_db ? $this->user_db->lookupByOId($oid) : null;
        if (is_null($existing_element)) {
            $this->logger->debug('User with oid does not exist, or data base lookup skipped.');
            $new_builder->element = $this->element
                ->withOid($oid);
        }
        if (!is_null($existing_element)) {
            $this->logger->debug('User with oid found');
            $new_builder->element = $existing_element;
        }
        return $new_builder;
    }

    public function withLogin(
        string $login
    ) : BuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element->withLogin($login);
        return $new_builder;
    }

    public function withPasswordStatus(
        Status $status
    ) : BuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element->withPasswordStatus($status);
        return $new_builder;
    }

    public function withCreationStatus(
        Status $status
    ) : BuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element->withCreationStatus($status);
        return $new_builder;
    }

    public function withImportFailure(
        bool $value
    ) : BuilderInterface {
        $new_builder = clone $this;
        $new_builder->element = $this->element->withImportStatusFailed($value);
        return $new_builder;
    }

    public function get() : UserDBElementInterface
    {
        return $this->element;
    }

    public function store() : void
    {
        $this->user_db->update($this->element);
    }
}
