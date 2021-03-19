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

use JsonSerializable;

interface ActionInterface extends JsonSerializable
{
    public function getActionType(): string;
}
