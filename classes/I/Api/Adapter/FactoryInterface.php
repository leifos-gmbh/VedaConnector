<?php

declare(strict_types=1);

namespace Leifos\VedaConnector\I\Api\Adapter;

interface FactoryInterface
{
    public function courseImport(): CourseImportInterface;

    public function courseStandardImport(): CourseStandardImportInterface;

    public function userImport(): UserImportInterface;

    public function memberImport(): MemberImportInterface;

    public function memberStandardImport(): MemberStandardImportInterface;
}
