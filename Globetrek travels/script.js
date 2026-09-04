

/* ================= NAVBAR SCROLL ================= */

const navbar = document.querySelector(".main-navbar");

window.addEventListener("scroll", function () {

    if (window.scrollY > 50) {

        navbar.classList.add("scrolled");

    } else {

        navbar.classList.remove("scrolled");

    }

});


/* ================= HOME SEARCH ================= */

const searchForm = document.getElementById("homeSearch");

if (searchForm) {

    searchForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const searchValue =
            document.getElementById("searchInput").value.trim();

        if (searchValue === "") {

            alert("Please enter a destination or package name.");

            return;

        }

        /*
            Pass the search keyword to the tour packages page.
        */

        window.location.href =
            "tour-packages.html?search=" +
            encodeURIComponent(searchValue);

    });

}

