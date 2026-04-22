<?php

namespace Leifos\VedaConnector\I\Mail;

interface HandlerInterface
{
    public function sendStatus(): void;

    public function sendSIFACourseCompleted(): void;
}
