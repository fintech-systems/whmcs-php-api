<?php

namespace FintechSystems\Whmcs\Contracts;

interface BillingProvider
{
    public function changePlan(array $data);
}
