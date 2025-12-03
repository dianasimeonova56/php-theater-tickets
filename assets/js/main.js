$(document).ready(function () {

    const urlParams = new URLSearchParams(window.location.search);
    const urlID = Number(urlParams.get('id'));
    let sc;

    //initialize seat chart
    function initSeatChart(playId) {
        if(playId === undefined) {
            $("#bookPlayBtn").prop("disabled", true);
            $("#seat-map, #booking-details").addClass("d-none");
            return;
        }

        $("#bookPlayBtn").prop("disabled", false);
            $("#seat-map, #booking-details").removeClass("d-none");
        
        if (sc == undefined) {
            sc = $('#seat-map').seatCharts({
                map: [
                    'aaaaaa__aaaaaa',
                    'aaaaaa__aaaaaa',
                    'aaaaaaaaaaaaaa'
                ],
                seats: {
                    a: { price: 10, classes: 'regular', category: 'Regular' }
                },
                naming: {
                    top: true,
                    getLabel: function (character, row, column) {
                        return column;
                    }
                },
                click: function () {

                    if (this.status() === "available") {
                        this.status('selected');
                    } else if (this.status() === "selected") {
                        this.status('available');
                    } else {
                        return this.style();
                    }
                    let selected = sc.find('selected');

                    let seatIds = [];
                    let total = 0;

                    selected.each(function () {
                        seatIds.push(this.settings.id);
                        total += this.data().price;
                    });

                    $('#selected-count').text(seatIds.length);
                    $('#selected-seats').text(seatIds.join(', '));
                    $('#total-price').text(total);

                    return this.status();
                },
                legend: {
                    node: $('#legend'),
                    items: [
                        ['a', 'available', 'Regular'],
                        ['a', 'unavailable', 'Reserved']
                    ]
                }
            });
        }


        setTimeout(() => {
            loadReservedSeats(playId);
        }, 150)
    }

    //USER: register
    $("#registerForm").on("submit", function (e) {
        e.preventDefault();
        $.post("../api/register.php", {
            username: $("#username").val(),
            first_name: $("#first_name").val(),
            last_name: $("#last_name").val(),
            email: $("#email").val(),
            phone: $("#phone").val(),
            password: $("#password").val(),
            repeat_password: $("#repeat_password").val(),
            recaptcha: grecaptcha.getResponse()
        },
            function (response) {
                let data = JSON.parse(response);
                if (data.status == "error") {
                    Swal.fire("Error", data.message, "error");
                    grecaptcha.reset();
                } else if (data.status == "success") {
                    Swal.fire("Success", data.message, 'success').then(() => { location.href = "login_page.php" });
                }
            }
        )
    })

    //USER: login
    $("#loginForm").on("submit", function (e) {
        e.preventDefault();
        $.post("../api/login.php", {
            usernameOrEmail: $("#usernameOrEmail").val(),
            password: $("#password").val(),
            recaptcha: grecaptcha.getResponse()
        },
            function (response) {
                let data = JSON.parse(response);
                if (data.status == "error") {
                    Swal.fire("Error", data.message, "error");
                    grecaptcha.reset();
                } else if (data.status == "success") {
                    location.href = "../index.php";
                }
            }
        )
    })

    //USER: logout
    $("#logout").on("click", function () {
        $.post("../api/logout.php",
            function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    location.href = "/kursowa/index.php";
                }
            }
        )
    })

    //PLAYS: add
    $("#addPlayForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "../api/create_play.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    Swal.fire("", data.message, "success");
                    location.href = "load_plays.php";
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            }
        }
        )
    })

    //PLAYS: load individual play for PLAYS page
    function loadPlays() {
        $.get("../api/load_plays.php", function (data) {

            let plays = JSON.parse(data);
            let grid = $("#playsGrid");
            grid.empty();
            let role = $("#playsGrid").data("role");

            let btns = "";

            plays['data'].forEach(play => {
                if (role == "admin ") {
                    btns = `<a href="edit_play.php?id=${play.id}" class="btn btn-warning w-50" id="editPlayBtn">Edit</a><a href="javascript:void(0)" class="btn btn-danger w-50" id="deletePlayBtn" data-id=${play.id}>Delete</a>`;
                } else if (role == "regular ") {
                    btns = `<a href="book_play.php?id=${play.id}" class="btn btn-primary w-100">Book Ticket</a>`;
                } else {
                    btns = '';
                }
                grid.append(`
                     <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="../assets/uploads/${play.image}" class="card-img-top" alt="${play.name}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase">${play.name}</h5>
                            <p class="card-text small text-muted mb-2">
                                <strong>Date:</strong> ${play.date}<br>
                                <strong>Duration:</strong> ${play.duration} min
                            </p>
                            <p class="card-text flex-grow-1">${play.description.substring(0, 100)}...</p>
                        </div>
                        <div class="card-footer text-center">
                            ${btns}
                        </div>
                    </div>
                </div>`)
            })
        })
    }

    //PLAYS: if on PLAYS page, load all plays
    if ($(".allPlays").length > 0) {
        loadPlays();
    }

    //PLAYS: load play names in select, if coming from PLAYS page (to book play) -> directly load the desired play
    function loadPlayNames() {
        $.get("../api/load_plays.php", function (data) {
            let plays = JSON.parse(data);
            let select = $("#playName");

            plays['data'].forEach(play => {
                select.append(`<option value='${play.id}' data-id='${play.id}'>${play.name}</option>`);
            })

            if (urlID) {
                $("#playName").val(Number(urlID));

                // $("#seat-map, #booking-details").removeClass("d-none");
                // $("#bookPlayBtn").prop("disabled", false);

                initSeatChart(urlID);
            }
        })
    }
    loadPlayNames();

    //PLAYS: load play for edit play 
    function loadPlay(urlID) {
        $.get(`../api/get_play.php?id=${urlID}`, function (data) {
            const play = JSON.parse(data);

            $('#name').val(play['data'].name);
            $('#description').val(play['data'].description);
            $('#date').val(play['data'].date);
            $('#duration').val(play['data'].duration);
        })
    };

    //PLAYS: load play for edit play with id from url search params
    if (urlID) {
        loadPlay(urlID);
    }


    //PLAYS: submit edit play form
    $("#editPlayForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "../api/edit_play.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    Swal.fire("", data.message, "success");
                    location.href = "load_plays.php";
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            }
        })
    });

    //PLAYS: delete play
    $(document).on("click", "#deletePlayBtn", function () {
        let playId = $(this).data("id");

        $.post("../api/delete_play.php", {
            id: playId
        }, function (response) {
            let data = JSON.parse(response);
            if (data.status == "success") {
                Swal.fire("", data.message, "success");
                loadPlays();
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
    });

    //PLAYS: load reserved seats for a play
    function loadReservedSeats(playId) {
        sc.find('unavailable').each(function () {
            this.status('available');
        });
        sc.find('selected').each(function () {
            this.status('available');
        });


        $.get(`../api/get_reserved_seats.php?playId=${playId}`, function (response) {
            let result = JSON.parse(response);

            if (result.status === "success") {

                result.data.forEach(seatId => {
                    let seat = sc.get(seatId);

                    if (seat) {
                        seat.status('unavailable');
                    } else {
                        console.warn("Seat not found:", seatId);
                    }
                });
            }
        });
    }

    //load data for a play on select change
    $("#playName").on("change", function () {
        let playId = $(this).find(":selected").data("id");

        $("#seat-map, #booking-details").removeClass("d-none");
        $("#bookPlayBtn").prop("disabled", false);

        initSeatChart(playId);
    })

    //BOOK PLAY
    $("#bookPlayForm").on("submit", function (e) {
        e.preventDefault();
        const playId = $('#playName option:selected').data('id');

        const selectedSeats = [];

        sc.find('selected').each(function () {
            selectedSeats.push({
                id: this.settings.id,
                price: this.data().price
            });
        });

        if (selectedSeats.length === 0) {
            Swal.fire("Error", "Please select at least one seat", "error");
            return;
        }

        let formData = new FormData();
        formData.append('playId', playId);
        formData.append('selectedSeats', JSON.stringify(selectedSeats));

        $.ajax({
            url: "../api/book_play.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    Swal.fire("", data.message, "success");
                    loadReservedSeats(playId);
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            }
        })
    })

    //USER: load user's profile info and bookings
    function loadProfile() {
        $.get("../api/load_profile.php", function (data) {
            const profile = JSON.parse(data);

            $('#first_name').val(profile['user'].first_name);
            $('#last_name').val(profile['user'].last_name);
            $('#username').val(profile['user'].username);
            $('#email').val(profile['user'].email);
            $('#phone').val(profile['user'].phone);

            const grid = $("#bookingsGrid");
            grid.empty();

            if (profile['bookings'].length != null && profile['bookings'][0].booking_id != null) {
                profile["bookings"].forEach(booking => {
                    grid.append(`
                     <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="../assets/uploads/${booking.play_image}" class="card-img-top" alt="${booking.play_name}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-uppercase">${booking.play_name}</h5>
                            <p class="card-text small text-muted mb-2">
                                <strong>Date: </strong>${booking.play_date}<br>
                                <strong>Tickets bought: </strong>${booking.tickets_count}<br>
                                <strong>Total price: </strong>${booking.total_price}<br>
                            </p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="javascript:void(0)" class="btn btn-danger w-50" id="deleteBookingBtn" data-id=${booking.booking_id}>Delete</a>
                        </div>
                    </div>
                </div>`)
                })
            } else {
                grid.append("Your bookings will show here!");
            }
        })
    }

    //USER: if on my profile page, load profile
    if ($(".myProfile").length > 0) {
        loadProfile();
    }

    //USER: update profile
    $("#myProfileForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "../api/edit_profile.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    Swal.fire("", data.message, "success");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            }
        })
    })

    //USER: change password
    $("#changePassword").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "../api/change_password.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                let data = JSON.parse(response);
                if (data.status == "success") {
                    Swal.fire("", data.message, "success");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            }
        })
    }
    )

    //BOOKING: delete booking
    $(document).on("click", "#deleteBookingBtn", function () {
        let bookingId = $(this).data("id");

        $.post("../api/delete_booking.php", {
            id: bookingId
        }, function (response) {
            let data = JSON.parse(response);
            if (data.status == "success") {
                Swal.fire("", data.message, "success");
                loadProfile();
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
    })
})