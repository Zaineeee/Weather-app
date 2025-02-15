<template>
    <nav class="navbar">
        <div class="logo">Weather Forecast</div>
        <div class="nav-links">
            <a href="#">About Us</a>
            <router-link to="/monitoring">Monitoring</router-link>
            <a href="#">Forecast</a>
            <div class="user" style="position: relative;">
                <div @click="toggleDropdown" style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    </svg>
                    <span>{{ userName }}</span>
                </div>
                <div v-show="isDropdownOpen" class="dropdown-content">
                    <a 
                        v-if="isAdmin" 
                        href="#" 
                        @click.prevent="navigateToDashboard"
                        class="dropdown-item"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="dropdown-icon">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a 
                        href="#" 
                        @click.prevent="handleLogout"
                        class="dropdown-item"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="dropdown-icon">
                            <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="search-container">
        <input 
            type="text" 
            class="search-bar" 
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            placeholder="Search for a city"
        >
        <button class="current-location-btn" @click="getCurrentLocation">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                <path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.46-4.17-3.77-7.48-7.94-7.94V2h-2v1.06C6.83 3.52 3.52 6.83 3.06 11H2v2h1.06c.46 4.17 3.77 7.48 7.94 7.94V22h2v-1.06c4.17-.46 7.48-3.77 7.94-7.94H22v-2h-1.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
            </svg>
            Current Location
        </button>
    </div>

    <div class="location-container">
        <div class="location-details">
            <h2 class="city-name">{{ locationDetails.city }}</h2>
            <p class="location-info">
                <span v-if="locationDetails.state">{{ locationDetails.state }}, </span>
                <span v-if="locationDetails.province && locationDetails.province !== locationDetails.state">
                    {{ locationDetails.province }}, 
                </span>
                <span>{{ locationDetails.country }}</span>
            </p>
        </div>
    </div>

    <div class="weather-container">
        <div class="weekly-forecast">
            <h3>6 Days Forecast:</h3>
            <div class="forecast-items">
                <div v-for="day in weeklyForecast" :key="day.date" class="forecast-item">
                    <span>{{ day.date }}</span>
                    <span>{{ day.temp }}°C {{ day.icon }}</span>
                </div>
            </div>
        </div>

        <div class="current-weather">
            <div class="temperature">{{ weatherData.temperature }}</div>
            <p>Feels like: {{ weatherData.feelsLike }}</p>
            <div class="weather-icon">{{ weatherData.weatherIcon }}</div>
            <p>{{ weatherData.weatherDescription }}</p>
            
            <div class="weather-details">
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 2.1c-5.5 0-10 4.5-10 10s4.5 10 10 10 10-4.5 10-10-4.5-10-10-10zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm1-13.9c-.8-.1-1.6 0-2.3.3-.7.3-1.4.8-1.9 1.4-.5.6-.8 1.3-1 2.1-.2.8-.1 1.6.1 2.4l.3-.1c.2-.8.5-1.5.9-2.1.4-.6 1-1.2 1.6-1.5.6-.4 1.3-.6 2-.7.7-.1 1.5 0 2.2.2l.3-.3c-.7-.9-1.5-1.6-2.2-1.7z"/>
                    </svg>
                    <p>{{ weatherData.humidity }}</p>
                    <p>Humidity</p>
                </div>
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12.7 2.7c-.4-.4-1-.4-1.4 0l-8 8c-.4.4-.4 1 0 1.4l8 8c.4.4 1 .4 1.4 0l8-8c.4-.4.4-1 0-1.4l-8-8zm-5.9 9.3l5.2-5.2v10.4l-5.2-5.2z"/>
                    </svg>
                    <p>{{ weatherData.windSpeed }} km/h</p>
                    <p>Wind Speed</p>
                </div>
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm1-13h-2v6h2V7zm0 8h-2v2h2v-2z"/>
                    </svg>
                    <p>{{ weatherData.pressure }} hPa</p>
                    <p>Pressure</p>
                </div>
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z"/>
                    </svg>
                    <p>{{ weatherData.uvIndex }}</p>
                    <p>UV Index</p>
                </div>
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8c-1.65 0-3-1.35-3-3s1.35-3 3-3 3 1.35 3 3-1.35 3-3 3zm1-13h-2v3h2V2zm0 17h-2v3h2v-3zM5.64 7.05l-1.41 1.41 2.12 2.12 1.41-1.41-2.12-2.12zm12.72 12.72l-1.41 1.41 2.12 2.12 1.41-1.41-2.12-2.12zM2 13h3v-2H2v2zm17 0h3v-2h-3v2zM7.05 18.36l2.12-2.12-1.41-1.41-2.12 2.12 1.41 1.41z"/>
                    </svg>
                    <p>{{ weatherData.sunrise }}</p>
                    <p>Sunrise</p>
                </div>
                <div class="detail-item">
                    <svg class="detail-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.65 0-3 1.35-3 3s1.35 3 3 3 3-1.35 3-3-1.35 3-3 3zm1-13h-2v3h2V2zm0 17h-2v3h2v-3zM5.64 7.05l-1.41 1.41 2.12 2.12 1.41-1.41-2.12-2.12zm12.72 12.72l-1.41 1.41 2.12 2.12 1.41-1.41-2.12-2.12zM2 13h3v-2H2v2zm17 0h3v-2h-3v2zM7.05 18.36l2.12-2.12-1.41-1.41-2.12 2.12 1.41 1.41z"/>
                    </svg>
                    <p>{{ weatherData.sunset }}</p>
                    <p>Sunset</p>
                </div>
            </div>

            <h3>Hourly Forecast:</h3>
            <div class="hourly-forecast">
                <div v-for="hour in hourlyForecast" :key="hour.time" class="hour-item">
                    <div class="hour">{{ hour.time }}</div>
                    <div class="weather-icon">{{ hour.icon }}</div>
                    <div class="temp">{{ hour.temp }}°C</div>
                </div>
            </div>
        </div>
    </div>

    
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const userName = ref('User')
const isDropdownOpen = ref(false)

// Weather data refs
const searchQuery = ref('')
const weatherData = ref({
    temperature: '--',
    feelsLike: '--',
    weatherIcon: '--',
    weatherDescription: '--',
    humidity: '--',
    windSpeed: '--',
    pressure: '--',
    uvIndex: '--',
    sunrise: '--:--',
    sunset: '--:--'
})
const weeklyForecast = ref([])
const hourlyForecast = ref([])
const apiKey = import.meta.env.VITE_OPENWEATHER_API_KEY || '1465a374cf73f59308efba12abe11b39'

// Add these new refs for location details
const locationDetails = ref({
    city: '',
    state: '',
    province: '',
    country: ''
})

// Add isAdmin ref
const isAdmin = ref(false)

// Add this function to get full country name
const getCountryName = async (countryCode) => {
    try {
        const response = await fetch(`https://restcountries.com/v3.1/alpha/${countryCode}`)
        if (!response.ok) throw new Error('Country not found')
        const data = await response.json()
        return data[0].name.common
    } catch (error) {
        console.error('Error fetching country name:', error)
        return countryCode // Return the code if we can't get the full name
    }
}

// Get weather by city name
const getWeatherByCity = async (city) => {
    try {
        const geoResponse = await fetch(
            `https://api.openweathermap.org/geo/1.0/direct?q=${city}&limit=1&appid=${apiKey}`
        )
        const geoData = await geoResponse.json()

        if (geoData.length === 0) {
            alert('City not found. Please check the spelling and try again.')
            return
        }

        const { lat, lon, country, state, name } = geoData[0]
        
        // Store the coordinates and city name for monitoring
        const weatherAlertData = {
            city: name,
            coordinates: { lat, lon },
            timestamp: Date.now()
        }
        
        // Store in localStorage
        localStorage.setItem('currentWeatherAlert', JSON.stringify(weatherAlertData))
        // Trigger update in monitoring component
        localStorage.setItem('weatherAlertUpdate', Date.now().toString())

        // Get full country name
        const fullCountryName = await getCountryName(country)
        
        // Update location details
        locationDetails.value = {
            city: name,
            state: state || '',
            province: state || '', // Some APIs use state for province
            country: fullCountryName
        }

        await getWeatherData(lat, lon)
    } catch (error) {
        console.error('Detailed city search error:', error)
        alert(`Error searching for city: ${error.message}`)
    }
}

// Get weather by coordinates
const getWeatherData = async (lat, lon) => {
    try {
        // Track API call start
        await trackApiCall('pending');
        
        // Get reverse geocoding data for more accurate location details
        const reverseGeoResponse = await fetch(
            `https://api.openweathermap.org/geo/1.0/reverse?lat=${lat}&lon=${lon}&limit=1&appid=${apiKey}`
        )
        if (!reverseGeoResponse.ok) {
            throw new Error('Failed to get location details')
        }
        const reverseGeoData = await reverseGeoResponse.json()
        
        if (reverseGeoData.length > 0) {
            const { name, country, state } = reverseGeoData[0]
            const fullCountryName = await getCountryName(country)
            
            locationDetails.value = {
                city: name,
                state: state || '',
                province: state || '',
                country: fullCountryName
            }
        }

        // Rest of your existing weather data fetching code...
        const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`
        const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`

        const [weatherResponse, forecastResponse] = await Promise.all([
            fetch(weatherUrl),
            fetch(forecastUrl)
        ])

        if (!weatherResponse.ok || !forecastResponse.ok) {
            await trackApiCall('error');
            throw new Error('Failed to fetch weather data')
        }

        const weatherData = await weatherResponse.json()
        const forecastData = await forecastResponse.json()

        // Track successful API call
        await trackApiCall('success');

        updateWeatherDisplay(weatherData, forecastData)
    } catch (error) {
        console.error('Detailed error:', error)
        if (error.message.includes('Invalid API key')) {
            alert('Invalid API key. Please check your OpenWeatherMap API key configuration.')
        } else {
            alert(`Error fetching weather data: ${error.message}`)
        }
    }
}

// Update weather display
const updateWeatherDisplay = (current, forecast) => {
    // Update current weather
    weatherData.value = {
        temperature: `${Math.round(current.main.temp)}°C`,
        feelsLike: `${Math.round(current.main.feels_like)}°C`,
        weatherIcon: getWeatherEmoji(current.weather[0].main),
        weatherDescription: current.weather[0].description,
        humidity: `${current.main.humidity}%`,
        windSpeed: `${Math.round(current.wind.speed * 3.6)} km/h`, // Convert m/s to km/h
        pressure: `${current.main.pressure} hPa`,
        uvIndex: 'N/A', // UV index not available in free tier
        sunrise: new Date(current.sys.sunrise * 1000).toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        }),
        sunset: new Date(current.sys.sunset * 1000).toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        })
    }

    // Update weekly forecast
    weeklyForecast.value = forecast.list
        .filter((item, index) => index % 8 === 0)
        .slice(0, 6)
        .map(day => ({
            date: new Date(day.dt * 1000).toLocaleDateString('en-US', { weekday: 'short' }),
            temp: Math.round(day.main.temp),
            icon: getWeatherEmoji(day.weather[0].main)
        }))

    // Update hourly forecast
    hourlyForecast.value = forecast.list
        .slice(0, 24)
        .map(hour => ({
            time: new Date(hour.dt * 1000).toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            }),
            temp: Math.round(hour.main.temp),
            icon: getWeatherEmoji(hour.weather[0].main)
        }))
}

// Get current location
const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                console.log('Location obtained:', {
                    lat: position.coords.latitude,
                    lon: position.coords.longitude
                })
                getWeatherData(position.coords.latitude, position.coords.longitude)
            },
            error => {
                console.error('Geolocation error:', error)
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert('Location access denied. Please enable location access or search for a city.')
                        break
                    case error.POSITION_UNAVAILABLE:
                        alert('Location information unavailable. Please search for a city instead.')
                        break
                    case error.TIMEOUT:
                        alert('Location request timed out. Please search for a city instead.')
                        break
                    default:
                        alert('Error getting location. Please search for a city instead.')
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        )
    } else {
        alert('Geolocation is not supported by this browser. Please search for a city instead.')
    }
}

// Weather emoji mapping
const getWeatherEmoji = (weatherType) => {
    const weatherEmojis = {
        'Clear': '☀️',
        'Clouds': '☁️',
        'Rain': '🌧️',
        'Snow': '❄️',
        'Thunderstorm': '⛈️',
        'Drizzle': '🌦️',
        'Mist': '🌫️'
    }
    return weatherEmojis[weatherType] || '⛅'
}

// Event handlers
const handleSearch = () => {
    if (searchQuery.value.trim()) {
        getWeatherByCity(searchQuery.value.trim())
    }
}

// Toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

// Close dropdown when clicking outside
const closeDropdown = (event) => {
    if (!event.target.closest('.user')) {
        isDropdownOpen.value = false
    }
}

// Handle logout
const handleLogout = () => {
    if (confirm('Are you sure you want to logout?')) {
        try {
            localStorage.clear()
            sessionStorage.clear()
            router.push('/')
        } catch (error) {
            console.error('Logout error:', error)
            router.push('/')
        }
    }
}

// Add dashboard navigation function
const navigateToDashboard = () => {
    router.push('/dashboard')
}

// Initialize weather data
onMounted(() => {
    // Check for saved weather data
    const savedData = localStorage.getItem('savedWeatherData')
    if (savedData) {
        try {
            const { weatherData: savedWeather, currentAlert, location } = JSON.parse(savedData)
            
            // Update the weather display with saved data
            if (savedWeather) {
                weatherData.value = savedWeather
            }
            
            // Get fresh weather data for the saved location
            if (location) {
                const [lon, lat] = location
                getWeatherData(lat, lon)
            } else {
                // Fallback to getting current location
                getCurrentLocation()
            }
            
            // Clear the saved data after using it
            localStorage.removeItem('savedWeatherData')
        } catch (error) {
            console.error('Error loading saved weather data:', error)
            // Fallback to getting current location
            getCurrentLocation()
        }
    } else {
        // No saved data, get current location
        getCurrentLocation()
    }
    
    document.addEventListener('click', closeDropdown)
    
    const storedName = localStorage.getItem('userName') || sessionStorage.getItem('userName')
    if (storedName) {
        userName.value = storedName
    }

    // Check if user is admin
    isAdmin.value = localStorage.getItem('isAdmin') === 'true'
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown)
})

// Add this function after your existing methods
const trackApiCall = async (status = 'success') => {
    try {
        // Get user data from localStorage
        const userData = JSON.parse(localStorage.getItem('user')) || {};
        
        // Get stored user info
        const userId = localStorage.getItem('userId');
        const userName = localStorage.getItem('userName');
        const userEmail = localStorage.getItem('email');
        
        const response = await fetch('/api/track-api-call', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                userId: userId || userData.userId || 'Unknown',
                userName: userName || userData.name || 'Unknown User',
                userEmail: userEmail || userData.email || 'N/A',
                endpoint: 'weather-api',
                status: status
            })
        });

        if (!response.ok) {
            throw new Error('Failed to track API call');
        }
    } catch (error) {
        console.error('Failed to track API call:', error);
    }
};
</script>

<style scoped>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(180deg, #4a6ba6 0%, #2b3c5e 100%);
            color: white;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(17, 24, 39, 0.8);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px;
            margin: 10px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 20px;
        }

        .search-container {
            margin: 40px auto;
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            max-width: 800px;
            padding: 0 20px;
        }

        .search-bar {
            padding: 15px 25px;
            width: 500px;
            height: 50px;
            border-radius: 25px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 1.2em;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .search-bar::placeholder {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1em;
        }

        .search-bar:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .current-location-btn {
            padding: 15px 25px;
            height: 50px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .current-location-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .current-location-btn svg {
            width: 24px;
            height: 24px;
        }

        .location-chip {
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 25px;
            border-radius: 25px;
            margin: 20px auto;
            display: block;
            width: fit-content;
            font-size: 1.2em;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .add-location {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .weather-container {
            display: flex;
            padding: 20px;
            gap: 20px;
            justify-content: center;
        }

        .weekly-forecast {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            width: 300px;
        }

        .weekly-forecast h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
            text-align: center;
        }

        .forecast-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 1.2em;
        }

        .forecast-item > div {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .forecast-item span {
            font-size: 1.1em;
        }

        .forecast-item div:first-child span {
            min-width: 100px;
        }

        .forecast-item div:last-child span {
            font-weight: bold;
        }

        .forecast-item div div {
            font-size: 1.5em;
        }

        .forecast-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        .current-weather {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            flex: 1;
            max-width: 850px;
            text-align: center;
        }

        .weather-icon {
            font-size: 48px;
            margin: 20px 0;
        }

        .temperature {
            font-size: 48px;
            margin: 10px 0;
        }

        .weather-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
            width: 100%;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.2s;
        }

        .detail-icon {
            width: 24px;
            height: 24px;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
        }

        .detail-item:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.2);
        }

        .detail-item:hover .detail-icon {
            color: white;
        }

        .detail-item p:first-of-type {
            font-size: 1.2em;
            font-weight: bold;
            margin: 5px 0;
        }

        .detail-item p:last-child {
            font-size: 0.9em;
            opacity: 0.8;
            margin: 0;
        }

        .hourly-forecast {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 20px 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .hourly-forecast::-webkit-scrollbar {
            height: 6px;
        }

        .hourly-forecast::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .hourly-forecast::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .hour-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 15px;
            min-width: 90px;
            text-align: center;
            transition: transform 0.2s;
        }

        .hour-item:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.3);
        }

        .user {
            position: relative;
            z-index: 100;
        }

        .dropdown-content {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: #111010;
            min-width: 180px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 0.4rem;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #222020;
        }

        .dropdown-icon {
            opacity: 0.7;
        }

        /* Animation for dropdown */
        .dropdown-content {
            transform-origin: top;
            animation: dropdownFade 0.2s ease;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Add responsive design for smaller screens */
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                padding: 0 15px;
            }

            .search-bar {
                width: 100%;
                max-width: 400px;
            }

            .current-location-btn {
                width: 100%;
                max-width: 400px;
                justify-content: center;
            }
        }

        .location-container {
            max-width: 400px;
            margin: 1.3rem auto;
            padding: .5rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .location-details {
            text-align: center;
        }

        .city-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #3a3636;
        }

        .location-info {
            font-size: 1.2rem;
            color: #696868;
        }
</style>