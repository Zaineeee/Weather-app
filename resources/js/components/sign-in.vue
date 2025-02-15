<template>
    <div class="page-wrapper">
        <div class="login-container">
            <h1>Welcome back</h1>
            <form @submit.prevent="handleLogin">
                <div v-if="errorMessage" class="error-message">
                    {{ errorMessage }}
                </div>
                <div class="form-group">
                    <input 
                        type="email" 
                        v-model="email" 
                        placeholder="Email" 
                        required
                    >
                </div>
                <div class="form-group">
                    <input 
                        type="password" 
                        v-model="password" 
                        placeholder="Password" 
                        required
                    >
                </div>
                <button type="submit">Sign In</button>
            </form>
            <div class="form-group signup-link">
                <span>Don't have an account? </span>
                <router-link to="/sign-up">Sign up</router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')
const errorMessage = ref('')

const handleLogin = async (event) => {
    event.preventDefault()
    errorMessage.value = ''
    
    try {
        // Check if it's admin login
        if (email.value === 'admin@weather.com' && password.value === 'admin123') {
            try {
                localStorage.setItem('isAdmin', 'true')
                localStorage.setItem('userName', 'Admin')
                localStorage.setItem('userEmail', email.value)
            } catch (error) {
                console.error('Storage error:', error)
                sessionStorage.setItem('isAdmin', 'true')
                sessionStorage.setItem('userName', 'Admin')
                sessionStorage.setItem('userEmail', email.value)
            }
            
            router.push('/landingpage')
            return
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.content
        if (!token) {
            throw new Error('CSRF token not found')
        }

        // Regular user login
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value
            })
        })

        const data = await response.json()

        if (!response.ok) {
            throw new Error(data.error || data.message || 'Login failed')
        }

        try {
            localStorage.setItem('userId', data.userId)
            localStorage.setItem('userEmail', data.email)
            localStorage.setItem('userName', data.name)
            localStorage.setItem('isAdmin', 'false')
            localStorage.setItem('apiCalls', JSON.stringify([]))
        } catch (error) {
            console.error('Storage error:', error)
            sessionStorage.setItem('userId', data.userId)
            sessionStorage.setItem('userEmail', data.email)
            sessionStorage.setItem('userName', data.name)
            sessionStorage.setItem('isAdmin', 'false')
        }

        router.push('/landingpage')
    } catch (error) {
        console.error('Login error:', error)
        errorMessage.value = error.message === 'CSRF token not found'
            ? 'Security token missing. Please refresh the page and try again.'
            : error.message || 'Error during login. Please try again.'
    }
}
</script>

<style scoped>
.page-wrapper {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(180deg, #4a6ba6 0%, #2b3c5e 100%);
}

.login-container {
    background: rgba(255, 255, 255, 0.1);
    padding: 40px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
}

h1 {
    color: white;
    text-align: center;
    margin-bottom: 30px;
    font-size: 24px;
}

.form-group {
    margin-bottom: 25px;
}

input {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 16px;
    box-sizing: border-box;
}

input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 5px;
    background: #cec9d9;
    color: rgb(6, 6, 6);
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: #651fff;
    color: white;
}

.signup-link {
    text-align: center;
    color: white;
    margin-top: 20px;
}

.signup-link a {
    color: #cec9d9;
    text-decoration: none;
    font-weight: bold;
}

.signup-link a:hover {
    color: #651fff;
}

.error-message {
    color: #ff4444;
    background-color: rgba(255, 68, 68, 0.1);
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}
</style>