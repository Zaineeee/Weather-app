<template>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-top">
                <div class="logo">
                    <span class="dashboard-title">Dashboard</span>
                </div>
            </div>
            
            <div class="sidebar-bottom">
                <button class="nav-button" @click="navigateToLanding">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="button-icon">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    Landing Page
                </button>
                <button class="logout-button" @click="handleLogout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="button-icon">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    Logout
                </button>
            </div>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>User Management</h1>
                <div class="search-bar">
                    <input 
                        type="text" 
                        v-model="searchInput"
                        placeholder="Search user..."
                        @input="searchUsers"
                        
                    >
                    <button @click="searchUsers">Filter</button>
                </div>
            </div>

            <div class="user-nav">
                <a 
                    href="#" 
                    :class="{ active: activeTab === 'overview' }"
                    @click.prevent="showTab('overview')"
                >Overview</a>
                <a 
                    href="#" 
                    :class="{ active: activeTab === 'lastLogin' }"
                    @click.prevent="showTab('lastLogin')"
                >Last Login</a>
                <a 
                    href="#" 
                    :class="{ active: activeTab === 'createdDate' }"
                    @click.prevent="showTab('createdDate')"
                >Created Date</a>
                <a 
                    href="#" 
                    :class="{ active: activeTab === 'address' }"
                    @click.prevent="showTab('address')"
                >Users</a>
                <a 
                    href="#" 
                    :class="{ active: activeTab === 'apiCalls' }"
                    @click.prevent="showTab('apiCalls')"
                >API Usage</a>
            </div>

            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="tab-content active">
                <div class="api-usage-card">
                    <h2>API Usage Overview</h2>
                    <div class="api-stats">
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.totalCalls }}</div>
                            <div class="stat-label">Total API Calls</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.todayCalls }}</div>
                            <div class="stat-label">Today's Calls</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.avgCalls }}</div>
                            <div class="stat-label">Average Daily Calls</div>
                        </div>
                    </div>
                </div>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Created Date</th>
                            <th>Last Login</th>
                            <th>API Calls</th>
                            <th>Last API Call</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone || 'N/A' }}</td>
                            <td>{{ new Date(user.createdDate).toLocaleString() }}</td>
                            <td>{{ formatDate(user.lastLogin) }}</td>
                            <td>{{ getUserApiCalls(user.id) }}</td>
                            <td>{{ getLastApiCall(user.id) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Last Login Tab -->
            <div v-if="activeTab === 'lastLogin'" class="tab-content active">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Login Time</th>
                            <th>Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="login in loginHistory" :key="login.id">
                            <td>{{ login.email }}</td>
                            <td>{{ formatDate(login.loginTime) }}</td>
                            <td>
                                <span :class="['session-status', login.sessionStatus.toLowerCase()]">
                                    {{ login.sessionStatus }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Created Date Tab -->
            <div v-if="activeTab === 'createdDate'" class="tab-content active">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in createdDates" :key="user.email">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ new Date(user.createdDate).toLocaleString() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Address Tab -->
            <div v-if="activeTab === 'address'" class="tab-content active">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>
                                <div v-if="editingUser?.id === user.id">
                                    <input v-model="editingUser.name" type="text" class="edit-input">
                                </div>
                                <span v-else>{{ user.name }}</span>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>
                                <div v-if="editingUser?.id === user.id">
                                    <input v-model="editingUser.address" type="text" class="edit-input">
                                </div>
                                <span v-else>{{ user.address || 'N/A' }}</span>
                            </td>
                            <td>
                                <div v-if="editingUser?.id === user.id">
                                    <input v-model="editingUser.phone" type="text" class="edit-input">
                                </div>
                                <span v-else>{{ user.phone || 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <template v-if="editingUser?.id === user.id">
                                        <button @click="saveUser" class="save-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                            </svg>
                                            Save
                                        </button>
                                        <button @click="cancelEdit" class="cancel-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                                            </svg>
                                            Cancel
                                        </button>
                                    </template>
                                    <template v-else>
                                        <button @click="editUser(user)" class="edit-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button @click="confirmDelete(user)" class="delete-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- API Calls Tab -->
            <div v-if="activeTab === 'apiCalls'" class="tab-content active">
                <h2>API Usage Tracking</h2>
                <div class="api-usage-card">
                    <h2>API Usage Overview</h2>
                    <div class="api-stats">
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.totalCalls }}</div>
                            <div class="stat-label">Total API Calls</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.todayCalls }}</div>
                            <div class="stat-label">Today's Calls</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">{{ statistics.avgCalls }}</div>
                            <div class="stat-label">Average Daily Calls</div>
                        </div>
                    </div>
                </div>

                <table class="user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>API Calls</th>
                            <th>Last API Call</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in apiCalls" :key="user.userId">
                            <td>{{ user.userId }}</td>
                            <td>{{ user.userName }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.totalCalls }}</td>
                            <td>{{ formatDate(user.lastCall) }}</td>
                        </tr>
                    </tbody>
                </table>

                <h3 style="margin-top: 30px;">Detailed API Call History</h3>
                <table class="usage-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Date & Time</th>
                            <th>API Endpoint</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="call in apiCallsDetailed" :key="call.id">
                            <td>{{ call.user_id }}</td>
                            <td>{{ call.user_name }}</td>
                            <td>{{ call.email }}</td>
                            <td>{{ formatDate(call.timestamp) }}</td>
                            <td>{{ call.endpoint }}</td>
                            <td>{{ call.status }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <button v-for="n in 7" :key="n" @click="changePage(n)">{{ n }}</button>
            </div>
        </div>
    </div>
    <Toast ref="toast" />
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import AlertModal from './AlertModal.vue'
import Toast from './Toast.vue'

const router = useRouter()
const activeTab = ref('overview')
const searchInput = ref('')
const apiCallsInterval = ref(null)
const apiRefreshInterval = ref(null)

// Data refs
const users = ref([])
const loginHistory = ref([])
const apiCalls = ref([])
const statistics = ref({
    totalCalls: 0,
    todayCalls: 0,
    avgCalls: 0
})
const createdDates = ref([])
const apiCallsDetailed = ref([])

// Add these new methods
const getUserApiCalls = (userId) => {
    return apiCalls.value.filter(call => call.userId === userId).length
}

const getLastApiCall = (userId) => {
    const userCalls = apiCalls.value.filter(call => call.userId === userId)
    if (userCalls.length === 0) return 'Never'
    
    const lastCall = userCalls.reduce((latest, current) => {
        return new Date(current.timestamp) > new Date(latest.timestamp) ? current : latest
    })
    return new Date(lastCall.timestamp).toLocaleString()
}

// Update the fetchUsers method to include API call data
const fetchUsers = async () => {
    try {
        const response = await fetch('/api/users', {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch users');
        }

        const data = await response.json();
        users.value = data;
    } catch (error) {
        console.error('Error fetching users:', error);
        showToast('Error', 'Failed to fetch users', 'error');
    }
}

// Update the fetchLoginHistory function
const fetchLoginHistory = async () => {
    try {
        const response = await fetch('/api/login-history')
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        const data = await response.json()
        console.log('Fetched login history:', data)
        loginHistory.value = data
    } catch (error) {
        console.error('Error fetching login history:', error)
    }
}

// Fetch API calls
const fetchApiCalls = async () => {
    try {
        const response = await fetch('/api/track-api-call')
        if (!response.ok) {
            throw new Error('Failed to fetch API calls')
        }
        
        const data = await response.json()
        
        // Update API calls stats
        apiCalls.value = data.userStats.map(user => ({
            userId: user.userId,
            userName: user.userName,
            email: user.email,
            totalCalls: user.totalCalls,
            lastCall: user.lastCall
        }))

        // Update detailed calls
        apiCallsDetailed.value = data.detailedCalls.map(call => ({
            user_id: call.user_id,
            user_name: call.user_name,
            email: call.email,
            timestamp: call.timestamp,
            endpoint: call.endpoint,
            status: call.status
        }))

        // Update statistics
        statistics.value = {
            totalCalls: data.statistics.totalCalls,
            todayCalls: data.statistics.todayCalls,
            avgCalls: data.statistics.avgCalls
        }

    } catch (error) {
        console.error('Error fetching API calls:', error)
    }
}

// Fetch created dates
const fetchCreatedDates = async () => {
    try {
        const response = await fetch('/api/created-dates')
        const data = await response.json()
        createdDates.value = data
    } catch (error) {
        console.error('Error fetching created dates:', error)
    }
}

// Update statistics
const updateStatistics = (calls) => {
    const today = new Date().toDateString()
    const todayCalls = calls.filter(call => 
        new Date(call.timestamp).toDateString() === today
    ).length

    const dates = [...new Set(calls.map(call => 
        new Date(call.timestamp).toDateString()
    ))]
    const avgCalls = dates.length ? Math.round(calls.length / dates.length) : 0

    statistics.value = {
        totalCalls: calls.length,
        todayCalls,
        avgCalls
    }
}

// Handle tab switching
const showTab = (tabName) => {
    activeTab.value = tabName
    switch(tabName) {
        case 'overview':
            fetchUsers()
            fetchApiCalls()
            break
        case 'lastLogin':
            fetchLoginHistory()
            break
        case 'createdDate':
            fetchCreatedDates()
            break
        case 'apiCalls':
            fetchApiCalls()
            break
    }
}

// Handle search
const searchUsers = () => {
    if (!searchInput.value) return
    const searchTerm = searchInput.value.toLowerCase()
    users.value = users.value.filter(user => 
        user.name.toLowerCase().includes(searchTerm) ||
        user.email.toLowerCase().includes(searchTerm)
    )
}

// Update the handleLogout function
const handleLogout = async () => {
    if (confirm('Are you sure you want to logout?')) {
        try {
            // Record logout first
            await fetch('/api/record-logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            // Refresh login history to update status
            await fetchLoginHistory();
            
            // Clear storage and redirect
            localStorage.clear();
            window.location.href = '/';
        } catch (error) {
            console.error('Error during logout:', error);
            // Fallback redirect
            localStorage.clear();
            window.location.href = '/';
        }
    }
}

// Check admin access and initialize data
onMounted(async () => {
    const isAdmin = localStorage.getItem('isAdmin') === 'true'
    if (!isAdmin) {
        alert('Access Denied. Only administrators can view this page.')
        router.push('/landingpage')
        return
    }

    await fetchApiCalls()
    await fetchUsers()
    await fetchLoginHistory()
    await fetchCreatedDates()

    // Set up auto-refresh for API calls every 10 seconds
    apiRefreshInterval.value = setInterval(async () => {
        await fetchApiCalls()
    }, 10000)
})

// Clean up
onUnmounted(() => {
    if (apiCallsInterval.value) {
        clearInterval(apiCallsInterval.value)
    }
    if (apiRefreshInterval.value) {
        clearInterval(apiRefreshInterval.value)
    }
})

// Add this watch to fetch login history when tab changes
watch(activeTab, (newTab) => {
    if (newTab === 'lastLogin') {
        fetchLoginHistory()
        const historyInterval = setInterval(fetchLoginHistory, 5000)
        onUnmounted(() => clearInterval(historyInterval))
    }
})

// Update the formatDate function
const formatDate = (date) => {
    if (!date) return 'Never';
    if (date === 'Active Session') return date;
    try {
        const options = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        return new Date(date).toLocaleString('en-US', options);
    } catch (error) {
        console.error('Date formatting error:', error);
        return date;
    }
}

// Add onMounted hook to fetch initial data
onMounted(() => {
    fetchLoginHistory()
    // Refresh login history every 5 seconds
    const historyInterval = setInterval(fetchLoginHistory, 5000)
    onUnmounted(() => clearInterval(historyInterval))
    fetchApiCalls()
    // Refresh API calls every 30 seconds
    setInterval(fetchApiCalls, 30000)
})

// Add navigation function
const navigateToLanding = () => {
    router.push('/landingpage')
}

// Add these refs
const editingUser = ref(null)

// Add CRUD functions
const editUser = (user) => {
    editingUser.value = { ...user }
}

const cancelEdit = () => {
    editingUser.value = null
}

const toast = ref(null)

// Replace showAlert with showToast
const showToast = (title, message, type = 'info') => {
    toast.value.addToast(title, message, type)
}

// Update your existing functions to use the new toast
const saveUser = async () => {
    try {
        const response = await fetch(`/api/users/${editingUser.value.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: editingUser.value.name,
                address: editingUser.value.address,
                phone: editingUser.value.phone
            })
        })

        if (!response.ok) throw new Error('Failed to update user')

        const index = users.value.findIndex(u => u.id === editingUser.value.id)
        if (index !== -1) {
            users.value[index] = { ...users.value[index], ...editingUser.value }
        }

        editingUser.value = null
        showToast('Success', 'User updated successfully', 'success')
    } catch (error) {
        console.error('Error updating user:', error)
        showToast('Error', 'Failed to update user', 'error')
    }
}

const confirmDelete = async (user) => {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        try {
            const response = await fetch(`/api/users/${user.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to delete user');
            }

            // Remove user from the list
            users.value = users.value.filter(u => u.id !== user.id);
            showToast('Success', 'User deleted successfully', 'success');

            // Refresh the data
            await fetchUsers();
        } catch (error) {
            console.error('Delete error:', error);
            showToast('Error', error.message || 'Failed to delete user', 'error');
        }
    }
}
</script>

<style scoped>
.user-table {
    width: 100%;
    overflow-x: auto;
    margin-top: 20px;
}

.api-usage-card {
    margin-bottom: 30px;
}

.session-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.session-status.active {
    background-color: #50e48e;
}

.edit-input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.edit-btn, .delete-btn, .save-btn, .cancel-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.edit-btn {
    background-color: #2196F3;
    color: white;
}

.delete-btn {
    background-color: #f44336;
    color: white;
}

.save-btn {
    background-color: #4CAF50;
    color: white;
}

.cancel-btn {
    background-color: #757575;
    color: white;
}

.edit-btn:hover { background-color: #1976D2; }
.delete-btn:hover { background-color: #D32F2F; }
.save-btn:hover { background-color: #388E3C; }
.cancel-btn:hover { background-color: #616161; }
</style>