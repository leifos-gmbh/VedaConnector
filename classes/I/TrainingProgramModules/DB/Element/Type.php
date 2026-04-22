<?php

namespace Leifos\VedaConnector\I\TrainingProgramModules\DB\Element;

enum Type: string
{
    case PRAKTIKUM = 'Praktikum';
    case SELF_LEARNING = 'Selbstlernen';
    case NULL = 'null';
}
