<?php

/*
 * CONNOX GMBH CONFIDENTIAL
 * ________________________
 *
 * Copyright (c) 2005 - 2021 Connox GmbH, All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains the property
 * of Connox GmbH and its suppliers, if any. The intellectual and
 * technical concepts contained herein are proprietary to Connox GmbH and
 * its suppliers and may be covered by German Laws and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material is
 * strictly forbidden unless prior written permission is obtained from
 * Connox GmbH.
 */

declare(strict_types=1);

namespace CodeDuck\Elasticsearch\Action;

/**
 * @psalm-immutable
 */
class Delete implements ActionInterface
{
    private array $document;

    public function __construct(string $id, string $index, string $type = '_doc')
    {
        $this->document = [
            '_id' => $id,
            '_type' => $type,
            '_index' => $index,
        ];
    }

    public function getActionType(): string
    {
        return 'delete';
    }

    public function jsonSerialize(): array
    {
        return $this->document;
    }
}
