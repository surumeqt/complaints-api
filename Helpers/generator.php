<?php

/**
 * Generates a 10-digit string: YYYYMM + 4 random digits
 */
function generateUserId()
{
    $yearMonth = date('Ym');
    $randomDigits = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    
    return $yearMonth . $randomDigits;
}
/**
 * Generates a 10-digit string: YYMMDD + 4 random digits
 */
function generateComplaintId() 
{
    $datePart = date("ymd");
    $randomPart = str_pad(mt_rand(0, 9999), 4, "0", STR_PAD_LEFT);
    
    return $datePart . $randomPart;
}
/**
 * Generates a 10-digit string: MMDDHHMM + 2 random digits
 */
function generateCategoryId() 
{
    $datePart = date("mdHi");
    $randomPart = str_pad(mt_rand(0, 99), 2, "0", STR_PAD_LEFT);
    
    return $datePart . $randomPart;
}
/**
 * Generates a 10-digit string: YYMMDD + 4 random digits
 */
function generateResponseId() 
{
    $datePart = date("ymd");
    $randomPart = str_pad(mt_rand(0, 9999), 4, "0", STR_PAD_LEFT);
    
    return $datePart . $randomPart;
}

// Quick Test:
// echo "Complaint: " . var_dump(generateComplaintId()) . "\n";
// echo "Category:  " . var_dump(generateCategoryId()) . "\n";
// echo "Response:  " . var_dump(generateResponseId()) . "\n";