<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>

    {{-- <script src="build/assets/app-l0sNRNKZ.js"></script>
    <script src="build/assets/app-BjkK5AXn.js"></script>


    <script>
        Echo.private('user.21')
            .listen('winch.worker.accepted', (e) => {
                console.log(e)
            });
    </script> --}}


    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"
        integrity="sha384-2huaZvOR9iDzHqslqwpR87isEmrfxqyWOF7hr7BY6KG0+hVKLoEXMPUJw3ynWuhO" crossorigin="anonymous">
    </script>
    <script>
        localStorage.setItem("apiToken", "6|XbhRg2K0T04NDrKl7HaJriWyEeMY1ac4u8QfIYom439ec0bc")

        
        const socket = io("ws://localhost:8080", {
            transports: ["websocket"],
            withCredentials: true,
            protocols: [7],
            query: {
                client: "js",
                version: "8.4.0-rc2",
                flash: false,
            },
            transportOptions: {
                websocket: {
                    path: "/app/ggulgdo2jdhymvblllf7"
                }
            },
            allowEIO3: true
        });

        socket.on("connect_error", (err) => {
            console.log(`connect_error due to ${err.message}`);
        });

        socket.io.on("error", (error) => {
            console.log(error)
        });
    </script>
</body>

</html>
