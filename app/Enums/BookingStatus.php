<?php

namespace App\Enums;

enum BookingStatus: number
{
  case PENDING = 0;
  case CONFIRMED = 1;
  case CANCELED = 2;
  case COMPLETED = 3;
}