<?php
require ('data.php');
require ('index.html');

//returns array kind of key -> value
function getPartsFromFullname($full_name_str) {
    $keys = ['surname', 'name', 'patronymic'];
    $values = explode(' ', $full_name_str);
    return array_combine($keys, $values);
}

function getFullnameFromParts($surname, $name, $patronymic) { 
    return $surname.' '.$name.' '.$patronymic;
}

function getShortName($full_name_str) {
    $temp = getPartsFromFullname($full_name_str);
    return $temp['name'].' '.mb_substr($temp['surname'], 0, 1).'.';
}

//returns: -1 - female; 1 - male; 0 - undefined gender 
function getGenderFromName($full_name_str) {
    $gender = 0;
    $full_name = getPartsFromFullname($full_name_str);
    $surname = $full_name['surname'];
    $name = $full_name['name'];
    $patronymic = $full_name['patronymic'];
    if (mb_substr($patronymic, -3) == 'вна')
        $gender --;
    if (mb_substr($patronymic, -2) == 'ич')
        $gender ++;
    if (mb_substr($name, -1) == 'а')
        $gender --;
        elseif (mb_substr($name, -1) == 'й' || mb_substr($name, -1) == 'н')
            $gender ++;
    if (mb_substr($surname, -2) == 'ва')
        $gender --;
        elseif (mb_substr($surname, -1) == 'в')
            $gender ++;
    return $gender <=> 0;
}

function getGenderDescription($persons_array) {
    $male_counter = 0;
    $female_counter = 0;
    $gen_counter = 0;
    for ($i = 0; $i < count($persons_array); $i ++)    
    {    
        $gen_counter ++;
        switch (getGenderFromName($persons_array[$i]['fullname'])) {
            case '1':
                $male_counter ++;
                break;
            case '-1':
                $female_counter ++;
                break;
        }
    }
    $result[] = round($female_counter / $gen_counter, 3) * 100;
    $result[] = round($male_counter / $gen_counter, 3) * 100;
    $result[] = 100 - $result[0] - $result[1];
    return <<<OUTPUT
    Гендерный состав аудитории: <br>
    --------------------------- <br>
    Женщины - $result[0]% <br>
    Мужчины - $result[1]% <br>
    Не удалось определить - $result[2]% <br>
OUTPUT;
}

function getPerfectPartner($surname, $name, $patronymic, $persons_array) {
    $surname_new = mb_convert_case($surname, MB_CASE_TITLE);
    $name_new = mb_convert_case($name, MB_CASE_TITLE);
    $patronymic_new = mb_convert_case($patronymic, MB_CASE_TITLE);
    $full_name = getFullnameFromParts($surname_new, $name_new, $patronymic_new);
    $gender1 = getGenderFromName($full_name);
    $x = 0;
    $rnd = 50 + round(rand(0, 5000) / 100, 2);
    do {
        $x = rand(0, count($persons_array) - 1);
        $gender2 = getGenderFromName($persons_array[$x]['fullname']);
    } while (($gender1 == 1 && $gender2 >= 0) || ($gender1 == -1 && $gender2 <= 0));
    return getShortName($full_name)." + ".getShortName($persons_array[$x]['fullname'])." = "."<br>"."\u{2661} идеально на {$rnd}% \u{2661}";
}
?>