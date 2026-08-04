<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <header id="myHeader" class="sticky top-0 sm:h-24 h-24 flex items-center bg-gradient-to-b from-black">
        <a href="https://www.tfli.co.uk/"><img id="logo" class="h-auto p-3 sm:p-4 w-20 sm:w-20 md:w-24 lg:w-28 xl:w-36" src="https://www.tfli.co.uk/wp-content/uploads/nav-files/images/logo-white.png"></a>
    </header>

    <main class="w-full max-w-lg bg-white rounded-lg shadow p-6 mt-12 mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?> - Url Shortener</h1>
        </div>