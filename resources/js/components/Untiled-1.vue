<template>    
    <nav class="navbar">
        <a href="#" class="navbar-brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Weather Forecast
        </a>

        <div class="navbar-links">
            <a href="#">About Us</a>
            <a href="/sign-in">Monitoring</a>
            <a href="#">Forecast</a>
            <div style="background: rgba(128, 128, 128, 0.2); padding: 10px; border-radius: 5px;">
                <span style="color: white; margin-right: 10px;">{{ userEmail }}</span>
                <a v-if="!isLoggedIn" href="/sign-in" class="login-btn" style="background: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 5px; text-decoration: none; margin-right: 10px;">Sign In</a>
                <a v-if="!isLoggedIn" href="/sign-up" class="register-btn" style="background: #2196F3; color: white; border: none; padding: 5px 10px; border-radius: 5px; text-decoration: none;">Sign Up</a>
                <button v-else @click="handleLogout" class="logout-btn">Logout</button>
            </div>
        </div>
    </nav>

    <div class="search-container">
        <input 
            type="text" 
            class="search-box" 
            v-model="searchQuery"
            @keyup.enter="getWeather(searchQuery)"
            placeholder="Search "
        >
        <button class="location-button" @click="getCurrentLocation">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V2h-2v1.06C6.83 3.52 3.52 6.83 3.06 11H2v2h1.06c.46 4.17 3.77 7.48 7.94 7.94V22h2v-1.06c4.17-.46 7.48-3.77 7.94-7.94H22v-2h-1.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
        </button>
    </div>

    <div class="weather-container">
        <div class="weather-card">
            <h2 style="margin-top: 20px;">{{ cityName }}</h2>
            <div class="location-details">
                <div v-if="locationState">{{ locationState }}, </div>
                <div>{{ locationCountry }}</div>
            </div>
            <div class="weather-icon">{{ currentWeatherIcon }}</div>
            <div class="temperature">{{ currentTemp }}</div>
            <div class="forecast-days">
                <div v-for="day in forecastDays" :key="day.date" class="forecast-day">
                    <span>{{ day.date }}</span>
                    <span>{{ day.temp }}°C {{ day.icon }}</span>
                </div>
            </div>
        </div>
        <div class="weather-card add-card" @click="addNewCity">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                <path d="M12 5v14m-7-7h14"/>
            </svg>
        </div>
    </div>

    <div class="hourly-forecast">
        <h3>Hourly Forecast for:  {{ cityName }}</h3>
        <div class="hourly-cards">
            
            <div v-for="hour in hourlyForecast" :key="hour.time" class="hour-card">
                <div class="hour">{{ hour.time }}</div>
                <div class="weather-icon">{{ hour.icon }}</div>
                <div class="temp">{{ hour.temp }}°C</div>
            </div>
        </div>
    </div>
</template>    

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Configure axios for the weather API
const weatherAxios = axios.create({
    withCredentials: false // Don't send credentials for weather API requests
})

// Reactive state
const apiKey = ref('1465a374cf73f59308efba12abe11b39')
const currentUserId = ref(null)
const isLoggedIn = ref(false)
const userEmail = ref('')
const cityName = ref('Loading...')
const locationDetails = ref('--')
const currentWeatherIcon = ref('--')
const currentTemp = ref('--°')
const forecastDays = ref([])
const hourlyForecast = ref([])
const searchQuery = ref('')
const locationState = ref('')
const locationCountry = ref('')

// Update locationAlertShown to use localStorage
const locationAlertShown = ref(localStorage.getItem('locationAlertShown') === 'true')

// Methods
const initializeApp = async () => {
    try {
        await checkLoginStatus()
        getCurrentLocation()
    } catch (error) {
        console.error('Error initializing app:', error)
        alert('Error initializing application. Please try again later.')
    }
}

const checkLoginStatus = async () => {
    try {
        const userId = localStorage.getItem('userId')
        const email = localStorage.getItem('userEmail')
        
        if (userId && email) {
            currentUserId.value = parseInt(userId)
            isLoggedIn.value = true
            userEmail.value = email
        } else {
            isLoggedIn.value = false
        }
    } catch (error) {
        console.error('Error checking login status:', error)
        isLoggedIn.value = false
    }
}

const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async position => {
                try {
                    const { latitude, longitude } = position.coords
                    
                    // Get detailed location information using reverse geocoding
                    const response = await fetch(
                        `https://api.openweathermap.org/geo/1.0/reverse?lat=${latitude}&lon=${longitude}&limit=1&appid=${apiKey.value}`
                    )
                    const [locationData] = await response.json()
                    
                    // Get detailed address using OpenStreetMap Nominatim API
                    const nominatimResponse = await fetch(
                        `https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`
                    )
                    const addressData = await nominatimResponse.json()
                    
                    // Update location details
                    cityName.value = addressData.address.city || 
                                   addressData.address.town || 
                                   addressData.address.village || 
                                   locationData.name
                                   
                    locationState.value = addressData.address.state || 
                                        addressData.address.province || 
                                        locationData.state || ''
                    
                    // Get full country name
                    const fullCountryName = await getCountryName(locationData.country)
                    locationCountry.value = fullCountryName
                    
                    // Get weather data
                    await getWeatherByCoords(latitude, longitude)
                    
                    // Show success message only once and persist the state
                    if (!locationAlertShown.value) {
                        alert(`Location found: ${cityName.value}, ${locationState.value}, ${locationCountry.value}`)
                        locationAlertShown.value = true
                        localStorage.setItem('locationAlertShown', 'true')
                    }
                } catch (error) {
                    console.error('Error getting location:', error)
                    if (!locationAlertShown.value) {
                        alert('Error getting location. Please try searching for your city instead.')
                        locationAlertShown.value = true
                        localStorage.setItem('locationAlertShown', 'true')
                    }
                }
            },
            error => {
                console.error('Geolocation error:', error)
                if (!locationAlertShown.value) {
                    let errorMessage = 'Unable to get your location. '
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Please enable location access in your browser settings.'
                            break
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Location information is unavailable.'
                            break
                        case error.TIMEOUT:
                            errorMessage += 'Location request timed out.'
                            break
                        default:
                            errorMessage += 'Please try searching for your city instead.'
                    }
                    
                    alert(errorMessage)
                    locationAlertShown.value = true
                    localStorage.setItem('locationAlertShown', 'true')
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        )
    } else {
        if (!locationAlertShown.value) {
            alert("Geolocation is not supported by this browser. Please search for your city instead.")
            locationAlertShown.value = true
            localStorage.setItem('locationAlertShown', 'true')
        }
    }
}

const getCountryName = async (countryCode) => {
    try {
        const response = await fetch(`https://restcountries.com/v3.1/alpha/${countryCode}`)
        const [countryData] = await response.json()
        return countryData.name.common
    } catch (error) {
        console.error('Error fetching country name:', error)
        return countryCode // Return the code if we can't get the full name
    }
}

const getWeatherByCoords = async (lat, lon) => {
    try {
        if (!apiKey.value) {
            console.error('API key is not initialized')
            throw new Error('API key not initialized')
        }

        const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey.value}`
        const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey.value}`

        const [weatherResponse, forecastResponse] = await Promise.all([
            fetch(weatherUrl),
            fetch(forecastUrl)
        ])

        const weatherData = await weatherResponse.json()
        const forecastData = await forecastResponse.json()

        if (!weatherResponse.ok || !forecastResponse.ok) {
            throw new Error('Failed to fetch weather data')
        }

        // Combine the data for display
        const combinedData = {
            city: {
                name: cityName.value, // Use the already set city name
                state: locationState.value,
                country: locationCountry.value
            },
            current: weatherData,
            list: forecastData.list
        }

        console.log('Weather data received:', combinedData)
        updateWeatherDisplay(combinedData)
    } catch (error) {
        console.error('Weather fetch error details:', error)
        alert('Error fetching weather data. Please try again later.')
    }
}

const getWeather = async (city) => {
    try {
        const geoResponse = await fetch(
            `https://api.openweathermap.org/geo/1.0/direct?q=${city}&limit=1&appid=${apiKey.value}`
        )
        const geoData = await geoResponse.json()

        if (geoData.length === 0) {
            alert('City not found')
            return
        }

        const { lat, lon, country } = geoData[0]
        
        // Get full country name before updating the display
        const fullCountryName = await getCountryName(country)
        
        await getWeatherByCoords(lat, lon)
        
        // Update location display with more details
        cityName.value = geoData[0].name
        locationState.value = geoData[0].state || ''
        locationCountry.value = fullCountryName
    } catch (error) {
        console.error('Error:', error)
        alert('Error fetching weather data')
    }
}

const updateWeatherDisplay = (data) => {
    try {
        cityName.value = data.city.name
        // Get state from API response if available
        locationState.value = data.city.state || ''
        locationCountry.value = data.city.country || ''
        currentTemp.value = `${Math.round(data.current.main.temp)}°`
        currentWeatherIcon.value = getWeatherEmoji(data.current.weather[0].main)
        
        // Update forecast days
        forecastDays.value = data.list
            .filter((item, index) => index % 8 === 0)
            .slice(0, 5)
            .map(day => ({
                date: new Date(day.dt * 1000).toLocaleDateString('en-US', { weekday: 'short' }),
                temp: Math.round(day.main.temp),
                icon: getWeatherEmoji(day.weather[0].main)
            }))

        // Update hourly forecast
        hourlyForecast.value = data.list
            .slice(0, 8)
            .map(hour => ({
                time: new Date(hour.dt * 1000).toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit' 
                }),
                temp: Math.round(hour.main.temp),
                icon: getWeatherEmoji(hour.weather[0].main)
            }))
    } catch (error) {
        console.error('Error updating display:', error)
        throw new Error('Failed to update weather display')
    }
}

const getWeatherEmoji = (weatherType) => {
    const weatherEmojis = {
        'Clear': '☀️',
        'Clouds': '☁️',
        'Rain': '🌧️',
        'Snow': '❄️',
        'Thunderstorm': '⛈️',
        'Drizzle': '️',
        'Mist': '🌫️'
    }
    return weatherEmojis[weatherType] || '⛅'
}

const handleLogout = () => {
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('userId')
        localStorage.removeItem('userEmail')
        isLoggedIn.value = false
        userEmail.value = ''
        window.location.href = 'sign-in.html'
    }
}

const addNewCity = () => {
    const city = prompt('Enter city name:')
    if (city) {
        getWeather(city)
    }
}

// Add function to reset location alert (if needed)
const resetLocationAlert = () => {
    locationAlertShown.value = false
    localStorage.removeItem('locationAlertShown')
}

// Optional: Reset alert after a certain time period (e.g., 24 hours)
onMounted(() => {
    const lastAlertTime = localStorage.getItem('locationAlertTimestamp')
    if (lastAlertTime) {
        const ONE_DAY = 24 * 60 * 60 * 1000 // 24 hours in milliseconds
        if (Date.now() - parseInt(lastAlertTime) > ONE_DAY) {
            resetLocationAlert()
        }
    }
    
    if (locationAlertShown.value) {
        localStorage.setItem('locationAlertTimestamp', Date.now().toString())
    }
})

// Initialize on mount
onMounted(() => {
    initializeApp()
})

// Reset location alert when component is unmounted
onUnmounted(() => {
    locationAlertShown.value = false
})
</script>

<style scoped>
.navbar {
    background: linear-gradient(to right, #111827, rgba(31, 41, 55, 0.48), #111827);
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar-brand {
    color: white;
    font-size: 1.5rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.navbar-links {
    display: flex;
    gap: 1rem;
}

.navbar-links a {
    color: white;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    transition: background-color 0.3s;
}

.navbar-links a:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.search-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1rem;
    position: relative;
}

.search-box {
    width: 100%;
    padding: 0.8rem 2.5rem 0.8rem 1rem;
    border-radius: 25px;
    border: none;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    font-size: 1rem;
}

.search-box::placeholder {
    color: rgba(255, 255, 255, 0.8);
}

.location-button {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: #4CAF50;
    border: none;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.weather-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(225px, 2fr));
    gap: 2rem;
    padding: 5rem;
    max-width: 1200px;
    margin: 0 auto;
    height: 100vh;
}

.weather-card {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 0.7rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.weather-icon {
    font-size: 3rem;
    margin: 1rem 0;
}

.temperature {
    font-size: 3.5rem;
    font-weight: bold;
}

.forecast-days {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.forecast-day {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.hourly-forecast {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 1.5rem;
    margin: 1rem;
    max-width: 745px;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
}

.hourly-forecast h3 {
    color: white;
   
    margin-bottom: 20px;
    font-size: 1em;
    display: flex;
    
    
   
}

.hourly-cards {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    padding: 0.5rem;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
}

.hour-card {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 15px;
    padding: 1rem;
    min-width: 100px;
    text-align: center;
    transition: transform 0.3s;
}

.hour-card:hover {
    transform: translateY(-5px);
}

.add-card {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.add-card:hover {
    background: rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
    .navbar-links {
        display: none;
    }
    
    .weather-container {
        grid-template-columns: 1fr;
    }
}

.location-details {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.9rem;
    margin-bottom: 1rem;
    margin-top: 0.1rem;
    display: flex;
    justify-content: center;
    gap: 0.25rem;
}

.logout-btn:hover {
    background: #cc0000 !important;
}

#userEmail {
    font-size: 0.9em;
    opacity: 0.9;
}

</style>