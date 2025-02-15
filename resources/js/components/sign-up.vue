<template>
    <div class="signup-container">
        <h1>Create an account</h1>
        <form @submit.prevent="handleSignup">
            <input type="text" v-model="name" placeholder="Name" required>
            <input type="tel" v-model="phone" placeholder="Phone Number" required>
            <input type="email" v-model="email" placeholder="Email" required>
            <input type="text" v-model="address" placeholder="Address" required>
            <input type="password" v-model="password" placeholder="Password" required>
            <button type="submit" class="register-btn">Register</button>
        </form>
        <div class="login-link">
            <router-link to="/sign-in">Already have an account? Sign in</router-link>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router';

const router = useRouter();
const name = ref('')
const phone = ref('')
const email = ref('')
const address = ref('')
const password = ref('')
const error = ref('')

async function handleSignup() {
    try {
        const uniqueId = `${Date.now()}-${Math.floor(Math.random() * 1000)}`
        
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.content
        if (!token) {
            throw new Error('CSRF token not found')
        }
        
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                userId: uniqueId,
                name: name.value,
                phone: phone.value,
                email: email.value,
                address: address.value,
                password: password.value
            })
        })

        const data = await response.json()

        if (response.ok) {
            try {
                localStorage.clear()
                localStorage.setItem('uniqueId', uniqueId)
                localStorage.setItem('fullName', name.value)
                localStorage.setItem('userName', name.value)
                localStorage.setItem('userEmail', email.value)
                localStorage.setItem('apiCalls', JSON.stringify([]))
                
                alert("Account created successfully!")
                router.push('/sign-in')
            } catch (storageError) {
                console.error('Storage error:', storageError)
                alert("Account created successfully! Please sign in.")
                router.push('/sign-in')
            }
        } else {
            error.value = data.message || data.error || "Registration failed"
            alert(error.value)
        }
    } catch (error) {
        console.error('Registration error:', error)
        if (error.message === 'CSRF token not found') {
            alert('Security token missing. Please refresh the page and try again.')
        } else {
            alert('Registration failed. Please try again.')
        }
    }
}
</script>

<style scoped>
.signup-container {
    background-color: rgba(255, 255, 255, 0);
    padding: 30px;
    border-radius: 8px;
    width: 300px;
    margin: 40px auto;
}

h1 {
    color: black;
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    background-color: rgba(5, 0, 0, 0.275);
    color: white;
    box-sizing: border-box;
}

input::placeholder {
    color: rgb(242, 236, 236);
}

.register-btn {
    width: 100%;
    padding: 10px;
    background-color: white;
    border: none;
    border-radius: 4px;
    color: #000000;
    font-weight: bold;
    cursor: pointer;
    margin-top: 15px;
}

.login-link {
    text-align: center;
    margin-top: 15px;
}

.login-link a {
    color: white;
    text-decoration: none;
    font-size: 14px;
}
</style>