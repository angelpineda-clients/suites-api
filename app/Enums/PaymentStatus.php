<?php

namespace App\Enums;

enum PaymentStatus: number
{
  case CREATED = 0;
  case SUCCEEDED = 1;
  case PENDING = 2;
  case FAILED = 3;
  case CANCELED = 4;
}