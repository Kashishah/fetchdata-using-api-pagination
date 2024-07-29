<?php

include_once('head.php');
?>

<body>

    <div class="container sticky-top">
        <div class="mt-5 d-flex justify-content-end">
            <div class="   search-input-button w-50">
                <form id="searchbarForm" method="POST" class="d-flex" role="search" id="searchForm">
                    <input class="form-control me-2" id="searchInput" name="search-value" type="search" placeholder="Search" aria-label="Search">
                    <button class="me-2 btn btn-outline-success" id="onSearch" type="submit">Search</button>
                    <button class="me-2 btn btn-outline-success" id="onReset" type="reset">reset</button>
                </form>
                <div id="error-message" class="text"></div>
            </div>
        </div>
    </div>

    <div class="container text-center mt-5 mb-5 d-flex flex-wrap">
        <nav aria-label="Page navigation example">
            <ul id="pagination-links" class="pagination">
                
                <li class="page-item ">

                </li>

            </ul>
        </nav>


        <div id="card-show" class="row g-3">

        </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="dynamic-pagination-paginatify\dist\jquery.pajinatify.js"></script>


<script>
    $(document).ready(function() {

        // Define global variables 
        $('#error-message').text('')
        const globalVar = {
            perPageItems: 12,
            items: 100
        };
        const range = Math.ceil(globalVar.items / globalVar.perPageItems);

        // Fetch and display initial data
        getData(getLocalStorageData());

        // Function to get page number from localStorage
        function getLocalStorageData() {
            return localStorage.getItem('activePage') || 1;
        }

        // function onReset() {
            $('#onReset').on('click', function(e) {
                // e.preventDefault();
                // e.preventDefault();
                localStorage.setItem('activePage', 1);
                setTimeout(function(){
                    getData(getLocalStorageData(),'');
                }, 1000);
                makePageActive();
            });
        // }

        // Function to fetch data from API
        function getData(PageNumber, searchQuery = '') {
            console.log('in getData');
            // console.log('searchquery ' + searchQuery)
            if (searchQuery == '') {
                searchQuery = $(searchInput).prop('value');
            }
            console.log(searchQuery);
            // console.log('with checking variable searchquery ' + searchQuery)
            const offset = (PageNumber - 1) * globalVar.perPageItems;
            // console.log(offset);
            if (searchQuery == '' || searchQuery == null) {
                console.log('searchquery is null');
                var apiUrl = `https://jsonplaceholder.typicode.com/posts?_start=${offset}&_limit=${globalVar.perPageItems}`;
            } else {
                console.log('searchquery is not null');
                var apiUrl = `https://jsonplaceholder.typicode.com/posts?_start=${offset}&_limit=${globalVar.perPageItems}&q=${encodeURIComponent(searchQuery)}`;
            }
            // console.log(apiUrl);
            $.ajax({
                url: apiUrl,
                method: "GET",
                success: function(data) {
                    createCards(data);
                    // console.log("Data retrieved successfully:", data);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", status, error);
                }
            });
        }



        // Function to create and display cards
        function createCards(data) {
            const cardShowDiv = $('#card-show');
            cardShowDiv.empty();

            data.forEach(item => {
                const card = `
                    <div class="col overflow-y-auto col-md-4 col-lg-3">
                        <div class="card" style="width: 18rem;">
                            <div class="card-header">
                                <h2>${item.id}</h2>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">${item.title}</h5>
                                <p class="card-text">${item.body}</p>
                            </div>
                           
                        </div>
                    </div>`;
                cardShowDiv.append(card);
            });
        }
         // <div class="card-footer">
                            //     <a href="#" class="btn btn-primary">Go somewhere</a>
                            // </div>

        // Create pagination links
        for (let i = 1; i <= range; i++) {
            var storedId = parseInt(getLocalStorageData());
            const activeClass = (i === storedId) ? ' active' : '';
            $('#pagination-links').append(`
                <li class="page-item${activeClass}" id="page-${i}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`);
        }

        // Set the active page based on localStorage
        function makePageActive() {
            const getLocalStorageValue = getLocalStorageData();
            $('.page-item').removeClass('active');
            $(`#page-${getLocalStorageValue}`).addClass('active');
        }


        // Handle pagination link clicks
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault(); // Prevent default anchor behavior
            const activePageID = $(this).data('page');

            localStorage.setItem("activePage", activePageID);
            $('.page-item').removeClass('active');
            $(`#page-${activePageID}`).addClass('active');

            getData(activePageID);
        });

        // get Searchbar form data
        $('#searchbarForm').on('submit', function(e) {
            e.preventDefault();
            var searchInput = $('#searchInput').val().trim();
            $('#error-message').text('')

            if (searchInput != '') {
                localStorage.setItem('activePage', 1);
                getData(getLocalStorageData(), searchInput);
                makePageActive();
            } else {
                $('#error-message').addClass('text-danger').text('Please first give input');
            }
        });
    });
</script>