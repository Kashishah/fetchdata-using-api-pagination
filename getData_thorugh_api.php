<?php

include_once('head.php');
?>

<body>

    <div class="container sticky-top">
        <div class="mt-5 d-flex justify-content-end">
            <div class="search-input-button w-50">
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


        <div id="card-show" class="row mt-5">

        </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="jquery.simplePagination.js"></script>

<script>
    $(document).ready(function() {
        $('#error-message').text('');

        // Define global variables 
        const globalVar = {
            perPageItems: 20,
            items: 100,
            url: `http://localhost/get-data-from-api-with-search-pagination/getData_thorugh_api.php#page-${getLocalStorageData()}`
        };
        const range = Math.ceil(globalVar.items / globalVar.perPageItems);


        // get Searchbar form data
        $('#searchbarForm').on('submit', function(e) {
            e.preventDefault();
            var searchInput = $('#searchInput').val().trim();
            $('#error-message').text('')

            if (searchInput != '') {
                localStorage.setItem('activePage', 1);
                $(location).attr('href', globalVar.url);
                getData(getLocalStorageData(), searchInput);
                makePageActive();
            } else {
                $('#error-message').addClass('text-danger').text('Please first give input');
            }
        });
        // get Searchbar form data end


        // On reset functionality
        function onReset(url) {
            // $('#onReset').on('click', function() {
                localStorage.setItem('activePage', 1);
                $(location).attr('href',url);
                setTimeout(function() {
                    getData(getLocalStorageData(), '');
                    makePageActive();
                }, 1000);
        };
        // On reset functionality end


        // Function to get active page number from localStorage
        function getLocalStorageData() {
            return localStorage.getItem('activePage') || 1;
        }
        // Function to get active page number from localStorage end


        //  Create a pagination links
        function makePaginationLinks() {
            $('#pagination-links').pagination({
                items: globalVar.items,
                itemsOnPage: globalVar.perPageItems,
                // cssStyle: 'dark-theme',
                onPageClick: function(pageNumber = getLocalStoragedata()) {
                    localStorage.setItem('activePage', pageNumber)
                    setTimeout(function() {
                        getData(pageNumber, '');
                    }, 200);
                    makePageActive();
                }
            });
        }
        //  Create a pagination links end


        // Set the active page based on localStorage
        function makePageActive() {
            var getLocalStorageValue = parseInt(getLocalStorageData());

            // First remove current and active class from existing tags 
            $('span.current').each(function() {
                var $a = $('<a>').attr('href', '#page-' + $(this).text()).text($(this).text()).addClass('page-link');
                $(this).replaceWith($a);
                $a.parent().removeClass('active');
            });
            $('a').removeClass('current'); // Remove the 'current' class from the newly converted <a> elements

            // First remove current and active class from existing tags end

            // replace anchor tag to span and give a active and current class 

            $(`a[href="#page-${getLocalStorageValue}"]`).each(function() {
                var $span = $('<span>').addClass('current').text($(this).text()); // Create a <span> element with the class "current"
                $(this).replaceWith($span); // Replace the <a> element with the new <span> element
                $span.parent().addClass('active'); // Add the 'active' class to the parent <li> element

                // replace anchor tag to span and give a active and current class end
            });
        }
        // Set the active page based on localStorage end




        // Function to fetch data from API
        function getData(PageNumber, searchQuery = '') {
            if (searchQuery == '') {
                searchQuery = $(searchInput).prop('value');
            }
            const offset = (PageNumber - 1) * globalVar.perPageItems;
            if (searchQuery == '' || searchQuery == null) {
                var apiUrl = `https://jsonplaceholder.typicode.com/posts?_start=${offset}&_limit=${globalVar.perPageItems}`;
            } else {
                var apiUrl = `https://jsonplaceholder.typicode.com/posts?_start=${offset}&_limit=${globalVar.perPageItems}&q=${encodeURIComponent(searchQuery)}`;
            }
            $.ajax({
                url: apiUrl,
                method: "GET",
                success: function(data) {
                    createCards(data);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", status, error);
                }
            });
        }
        // Function to fetch data from API end



        // Function to create and display cards
        function createCards(data) {
            const cardShowDiv = $('#card-show');
            cardShowDiv.empty();

            data.forEach(item => {
                const card = `
                    <div class="col col-sm-12 col-md-6 col-lg-4">
                        <div class="card" >
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
        // Function to create and display cards end


     

        // Fetch and display initial data
        getData(getLocalStorageData());

        makePaginationLinks();

        $('#onReset').on('click', function() {
            onReset(globalVar.url);
        });


        makePageActive();

        window.location.href = globalVar.url;

    });
</script>