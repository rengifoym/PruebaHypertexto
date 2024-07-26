<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiController extends Controller
{
    public function getMostUsedLetter()
    {
        // Obtener 5 usuarios de la API externa
        $response = Http::get('https://randomuser.me/api/?results=5');
        
        // Decodificar la respuesta JSON
        $data = $response->json();
        
        // Extraer los nombres completos
        $names = array_column($data['results'], 'name');
        $fullNames = array_map(fn($name) => $name['first'] . ' ' . $name['last'], $names);
        
        // Concatenar todos los nombres completos en un solo string
        $allNamesString = implode(' ', $fullNames);
        
        // Convertir el string a minúsculas y eliminar caracteres no alfabéticos
        $cleanString = strtolower(preg_replace('/[^a-z]/', '', $allNamesString));
        
        // Convertir el string limpio en un array de caracteres
        $charactersArray = str_split($cleanString);
        
        // Contar la frecuencia de cada letra
        $lettersFrequency = array_count_values($charactersArray);
        
        // Encontrar la letra más frecuente
        $mostFrequentLetter = array_search(max($lettersFrequency), $lettersFrequency);
        
        // Devolver la respuesta con los nombres completos y la frecuencia
        return response()->json([
            'Nombres' => $fullNames,
            'Letra_Mas_Frecuente' => $mostFrequentLetter,
            'Frecuencia' => $lettersFrequency[$mostFrequentLetter],
        ]);
    }
}
