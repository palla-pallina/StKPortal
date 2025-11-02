const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item => {
	const li = item.parentElement;

	item.addEventListener('click', function (e) {
		e.preventDefault();
		
		// Remove active class from all menu items
		allSideMenu.forEach(i => {
			i.parentElement.classList.remove('active');
		});
		
		// Add active class to clicked item
		li.classList.add('active');
	});
});

const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
	sidebar.classList.toggle('hide');
});

function updateDateTime() {
    const now = new Date();

    const optionsDate = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = now.toLocaleDateString(undefined, optionsDate);

    const optionsTime = { hour: '2-digit', minute: '2-digit' };
    const time = now.toLocaleTimeString(undefined, optionsTime);

    document.getElementById('datetime').textContent = `${date} | ${time}`;
}

setInterval(updateDateTime, 1000);
updateDateTime();

// Donations functionality
let currentPage = 1;
let currentSearch = '';
let currentSort = 'date_posted DESC';
let currentFilterCateg = '';
let currentFilterType = '';

// Load donations
function loadDonations() {
    const loadingEl = document.getElementById('donationsLoading');
    if (loadingEl) loadingEl.classList.add('active');

    const formData = new FormData();
    formData.append('action', 'get_donations');
    formData.append('search', currentSearch);
    formData.append('sort', currentSort);
    formData.append('page', currentPage);
    formData.append('filter_donation_categ', currentFilterCateg);
    formData.append('filter_donation_type', currentFilterType);

    fetch('donations_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayDonations(data.donations);
            updatePagination(data.total, data.pages, data.current_page);
            document.getElementById('totalAmount').textContent = data.total_amount;
        } else {
            console.error('Error loading donations:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        if (loadingEl) loadingEl.classList.remove('active');
    });
}

// Display donations in table
function displayDonations(donations) {
    const tbody = document.getElementById('donationsTableBody');
    tbody.innerHTML = '';

    if (!donations || donations.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 12;
        cell.className = 'no-data';
        cell.textContent = 'No donations found.';
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('appear');
        
        const donationDisplay = donation.donation_categ === 'Specified' 
            ? `${donation.donation_categ} - ${donation.donation_specified}`
            : donation.donation_categ;
            
        const amountDisplay = donation.donation_amount 
            ? `P${parseFloat(donation.donation_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`
            : '-';
            
        const dateDisplay = new Date(donation.date_posted).toISOString().split('T')[0];

        row.innerHTML = `
            <td>${donation.donate_id}</td>
            <td>${donation.donor_fullname || 'Anonymous'}</td>
            <td>${donation.username || '-'}</td>
            <td>${donation.donor_email || '-'}</td>
            <td>${donationDisplay}</td>
            <td>${donation.donation_type}</td>
            <td>${donation.mode_of_payment}</td>
            <td>${donation.proof_of_donation ? `<a href="uploads/${donation.proof_of_donation}" target="_blank" class="proof-link">${donation.proof_of_donation}</a>` : '-'}</td>
            <td>${dateDisplay}</td>
            <td>${amountDisplay}</td>
            <td class="confirm-column">
                ${donation.donation_status === 'pending' ? `
                    <button class="btn-reject" onclick="updateDonationStatus(${donation.donate_id}, 'unverified')" title="Reject">
                        <i class='bx bx-x'></i>
                    </button>
                    <button class="btn-approve" onclick="updateDonationStatus(${donation.donate_id}, 'verified')" title="Approve">
                        <i class='bx bx-check'></i>
                    </button>
                ` : donation.donation_status === 'verified' ? `
                    <i class='bx bx-check' style="color: #16a34a; font-size: 18px;"></i>
                ` : `
                    <i class='bx bx-x' style="color: #dc2626; font-size: 18px;"></i>
                `}
            </td>
            <td class="action-column">
                <button class="btn-delete" onclick="deleteDonation(${donation.donate_id})" title="Delete">
                    <i class='bx bx-trash'></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

// Update pagination
function updatePagination(total, pages, currentPage) {
    const start = (currentPage - 1) * 8 + 1;
    const end = Math.min(currentPage * 8, total);
    
    document.getElementById('paginationInfo').textContent = `${start} - ${end} of ${total} Entries`;
    
    const pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';
    
    for (let i = 1; i <= pages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        if (i === currentPage) {
            button.classList.add('active');
        }
        button.onclick = () => goToPage(i);
        pageNumbers.appendChild(button);
    }
    
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === pages;
}

// Go to specific page
function goToPage(page) {
    currentPage = page;
    loadDonations();
}

// Update donation status
function updateDonationStatus(donateId, status) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('donate_id', donateId);
    formData.append('status', status);

    fetch('donations_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadDonations();
        } else {
            console.error('Error updating status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Delete donation
function deleteDonation(donateId) {
    if (confirm('Are you sure you want to delete this donation?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('donate_id', donateId);

        fetch('donations_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadDonations();
            } else {
                console.error('Error deleting donation:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Modal functions
function openDonationModal() {
    document.getElementById('donationModal').style.display = 'block';
}

function closeDonationModal() {
    document.getElementById('donationModal').style.display = 'none';
    document.getElementById('donationForm').reset();
    document.getElementById('specifiedGroup').style.display = 'none';
}

// Event listeners for donations
document.addEventListener('DOMContentLoaded', function() {
    // Add donation button
    const addBtn = document.getElementById('addDonationBtn');
    if (addBtn) {
        addBtn.addEventListener('click', openDonationModal);
    }

    // Close modal
    const closeBtn = document.querySelector('.close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeDonationModal);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('donationModal');
        if (event.target === modal) {
            closeDonationModal();
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchDonations');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value;
                currentPage = 1;
                loadDonations();
            }, 500);
        });
    }

    // Sort functionality
    const sortSelect = document.getElementById('sortDonations');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            currentPage = 1;
            loadDonations();
        });
    }

    // Filter functionality
    const filterDonationCateg = document.getElementById('filterDonationCateg');
    const filterDonationType = document.getElementById('filterDonationType');
    if (filterDonationCateg) {
        filterDonationCateg.addEventListener('change', function() {
            currentFilterCateg = this.value;
            currentPage = 1;
            loadDonations();
        });
    }
    if (filterDonationType) {
        filterDonationType.addEventListener('change', function() {
            currentFilterType = this.value;
            currentPage = 1;
            loadDonations();
        });
    }

    // Pagination buttons
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            goToPage(currentPage + 1);
        });
    }

    // Donation form submission
    const donationForm = document.getElementById('donationForm');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add');

            fetch('donations_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDonationModal();
                    loadDonations();
                    alert('Donation added successfully!');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the donation.');
            });
        });
    }

    // Handle specified donation category
    const categorySelect = document.getElementById('donation_categ');
    const specifiedGroup = document.getElementById('specifiedGroup');
    
    if (categorySelect && specifiedGroup) {
        categorySelect.addEventListener('change', function() {
            if (this.value === 'Specified') {
                specifiedGroup.style.display = 'block';
                document.getElementById('donation_specified').required = true;
            } else {
                specifiedGroup.style.display = 'none';
                document.getElementById('donation_specified').required = false;
            }
        });
    }
});
