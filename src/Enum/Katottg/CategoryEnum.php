<?php

namespace App\Enum\Katottg;

/**
 * O - Автономна Республіка Крим, області
 * K - міста, що мають спеціальний статус
 * P - райони в областях та Автономній Республіці Крим
 * H - території територіальних громад (назви територіальних громад) в областях, територіальні громади Автономної Республіки Крим
 * M - міста
 * T - селища міського типу
 * C - села
 * X - селища
 * B - райони в містах
 */
enum CategoryEnum: string
{
    case Region = 'O';                 // область або АР Крим
    case CityWithSpecialStatus = 'K';  // місто зі спецстатусом
    case District = 'P';               // район області/АРК
    case Community = 'H';              // територіальна громада
    case City = 'M';                   // місто
    case UrbanTypeSettlement = 'T';    // смт
    case Village = 'C';                // село
    case Settlement = 'X';             // селище
    case CityDistrict = 'B';           // район у місті
}

