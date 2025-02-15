<template>
    <nav class="navbar">
        <div class="logo">Weather Forecast</div>
        <div class="nav-links">
            <router-link to="/about" class="nav-link">About Us</router-link>
            <router-link to="/monitoring" class="nav-link">Monitoring</router-link>
            <router-link to="/landingpage" class="nav-link">Forecast</router-link>
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

    <div class="content-container">
        <div class="left-column">
            <div class="alert-box">
                <h3><span class="alert-icon">⚠️</span> Weather Alert System <span class="alert-icon">⚠️</span></h3>
                <div class="alert-content">
                    <p v-if="!currentAlert">Monitoring weather conditions...</p>
                    <template v-else>
                        <p><strong>Current Alerts for {{ currentAlert.city }}</strong></p>
                        <p><small>Last updated: {{ currentAlert.time }}</small></p>
                        <p v-for="(alert, index) in currentAlert.alerts" :key="index">
                            {{ alert }}
                        </p>
                    </template>
                </div>
            </div>
            
            <div class="weather-details">
                <h3>Weather Forecast</h3>
                <div v-if="weatherData" class="weather-forecast">
                    <div class="location-info">
                        <h4>Location: {{ weatherData.location }}</h4>
                        <p>Issued at: {{ weatherData.timestamp }}</p>
                    </div>
                    <div class="current-conditions">
                        <h4>Current Weather</h4>
                        <p>Temperature: {{ weatherData.temperature }}°C</p>
                        <p>Feels like: {{ weatherData.feelsLike }}°C</p>
                        <p>Humidity: {{ weatherData.humidity }}%</p>
                        <p>Wind: {{ weatherData.windSpeed }} km/h</p>
                        <p>Condition: {{ weatherData.condition }}</p>
                    </div>

                    <div class="synopsis">
                        <h4>Weather Synopsis:</h4>
                        <p>{{ weatherData.synopsis }}</p>
                    </div>
                </div>
                <p v-else>Loading forecast data...</p>
            </div>
        </div>
        
        <div class="weather-map">
            <div class="map-container">
                <h3>Weather Map</h3>
                <div id="weatherMapContainer"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import 'ol/ol.css'
import Map from 'ol/Map'
import View from 'ol/View'
import TileLayer from 'ol/layer/Tile'
import OSM from 'ol/source/OSM'
import XYZ from 'ol/source/XYZ'
import { fromLonLat } from 'ol/proj'
import Feature from 'ol/Feature'
import Point from 'ol/geom/Point'
import VectorLayer from 'ol/layer/Vector'
import VectorSource from 'ol/source/Vector'
import { Style, Circle, Fill, Stroke } from 'ol/style'

const router = useRouter()
const API_KEY = '1465a374cf73f59308efba12abe11b39'
const userName = ref('User')
const isDropdownOpen = ref(false)
const currentAlert = ref(null)
const weatherData = ref(null)
let map = null
let markers = []
let markerLayer = null
const isAdmin = ref(false)
const monitoringInterval = ref(null)

// Safely get user name from storage
try {
    userName.value = localStorage.getItem('userName') || 'User'
} catch (error) {
    console.error('Storage access error:', error)
    // Handle the error gracefully
}

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value
}

const handleLogout = async () => {
    if (confirm('Are you sure you want to logout?')) {
        try {
            localStorage.clear()
            sessionStorage.clear()
        } catch (error) {
            console.error('Storage clear error:', error)
            // Handle the error gracefully
        }
        router.push('/')
    }
}

const navigateToLanding = () => {
    try {
        // Save current weather data before navigation
        const currentWeatherData = {
            weatherData: weatherData.value,
            currentAlert: currentAlert.value,
            location: map?.getView().getCenter()
        }
        
        // Check if localStorage is available
        if (typeof window !== 'undefined' && window.localStorage) {
            // Store it in localStorage
            localStorage.setItem('savedWeatherData', JSON.stringify(currentWeatherData))
        } else {
            // Fallback to sessionStorage
            sessionStorage.setItem('savedWeatherData', JSON.stringify(currentWeatherData))
        }
    } catch (error) {
        console.error('Storage error:', error)
        // Continue with navigation even if storage fails
    } finally {
        // Navigate to landing page
        router.push('/landingpage')
    }
}

const navigateToDashboard = () => {
    router.push('/dashboard')
}

// Weather API Functions
const fetchWeatherData = async (lat, lon) => {
    try {
        await trackApiCall('pending');
        
        const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${API_KEY}`;
        const response = await fetch(weatherUrl);
        
        if (!response.ok) {
            await trackApiCall('error');
            throw new Error('Failed to fetch weather data');
        }

        const data = await response.json();
        
        // Track successful API call
        await trackApiCall('success');

        weatherData.value = {
            location: data.name,
            timestamp: new Date().toLocaleString(),
            temperature: Math.round(data.main.temp),
            feelsLike: Math.round(data.main.feels_like),
            humidity: data.main.humidity,
            windSpeed: Math.round(data.wind.speed * 3.6),
            condition: data.weather[0].description,
            synopsis: generateWeatherSynopsis(data)
        };

        // Generate alerts based on conditions
        generateWeatherAlert(data);
    } catch (error) {
        console.error('Error fetching weather data:', error);
        await trackApiCall('error');
    }
};

const generateWeatherAlert = (data) => {
    const alerts = []
    const temp = data.main.temp
    const humidity = data.main.humidity
    const windSpeed = data.wind.speed
    const condition = data.weather[0].main

    // Temperature alerts
    if (temp > 35) {
        alerts.push(`🌡️ Extreme Heat Alert: Temperature is ${Math.round(temp)}°C`)
    } else if (temp < 10) {
        alerts.push(`❄️ Cold Weather Alert: Temperature is ${Math.round(temp)}°C`)
    }

    // Weather condition alerts
    switch (condition.toLowerCase()) {
        case 'thunderstorm':
            alerts.push('⛈️ Severe Weather Alert: Thunderstorms detected')
            break
        case 'rain':
            alerts.push('🌧️ Rain Alert: Precipitation expected')
            break
        case 'snow':
            alerts.push('❄️ Snow Alert: Snowy conditions')
            break
    }

    // Wind alerts
    if (windSpeed > 10) {
        alerts.push(`💨 Wind Advisory: Strong winds of ${Math.round(windSpeed * 3.6)} km/h`)
    }

    // Humidity alerts
    if (humidity > 80) {
        alerts.push(`💧 High Humidity Alert: ${humidity}% humidity`)
    }

    currentAlert.value = {
        city: data.name,
        time: new Date().toLocaleString(),
        alerts: alerts.length ? alerts : ['✅ No severe weather alerts at this time.']
    }
}

const generateWeatherSynopsis = (data) => {
    const conditions = []
    const temp = data.main.temp
    const windSpeed = data.wind.speed
    const weatherType = data.weather[0].main.toLowerCase()

    if (temp > 35) {
        conditions.push("Extreme heat conditions")
    } else if (temp < 10) {
        conditions.push("Cold weather conditions")
    }

    if (weatherType === 'thunderstorm') {
        conditions.push("Thunderstorm conditions present")
    }
    if (weatherType === 'rain') {
        conditions.push("Rainy conditions")
    }

    if (windSpeed > 10) {
        conditions.push("Strong winds present")
    }

    return conditions.length ? conditions.join(". ") + "." : "Generally stable weather conditions."
}

// Map Functions
const initializeMap = () => {
    map = new Map({
        target: 'weatherMapContainer',
        layers: [
            new TileLayer({
                source: new OSM()
            }),
            new TileLayer({
                source: new XYZ({
                    url: `https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=${API_KEY}`
                }),
                opacity: 0.6
            })
        ],
        view: new View({
            center: fromLonLat([125.488, 9.757]), // Surigao coordinates
            zoom: 8
        })
    })
}

const startMonitoring = () => {
    // Get initial location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const { latitude, longitude } = position.coords
                fetchWeatherData(latitude, longitude)
                updateMapView(latitude, longitude)
            },
            error => {
                console.error('Geolocation error:', error)
                // Default to Surigao coordinates
                fetchWeatherData(9.757, 125.488)
            }
        )
    }

    // Set up periodic updates
    setInterval(() => {
        if (weatherData.value) {
            fetchWeatherData(9.757, 125.488) // Update for current location
        }
    }, 300000) // Update every 5 minutes
}

const updateMapView = (lat, lon) => {
    if (map) {
        map.getView().animate({
            center: fromLonLat([lon, lat]),
            duration: 1000,
            zoom: 10  // Zoom in closer to searched location
        })
    }
}

const monitorWeatherSearches = async () => {
    try {
        if (typeof window === 'undefined' || !window.localStorage) {
            return // Exit if localStorage is not available
        }

        const weatherAlertUpdate = localStorage.getItem('weatherAlertUpdate')
        const lastUpdate = localStorage.getItem('lastWeatherUpdate')
        
        if (weatherAlertUpdate && weatherAlertUpdate !== lastUpdate) {
            const alertData = JSON.parse(localStorage.getItem('currentWeatherAlert'))
            if (alertData) {
                // Update weather data
                await fetchWeatherData(alertData.coordinates.lat, alertData.coordinates.lon)
                
                // Update map view and add marker
                updateMapView(alertData.coordinates.lat, alertData.coordinates.lon)
                addMarker(alertData.coordinates, alertData.city)
                
                localStorage.setItem('lastWeatherUpdate', weatherAlertUpdate)
            }
        }
    } catch (error) {
        console.error('Error monitoring weather searches:', error)
        // Handle the error gracefully - maybe set a default location
        const defaultLat = 9.757
        const defaultLon = 125.488
        await fetchWeatherData(defaultLat, defaultLon)
    }
}

const generateWeatherAlerts = (weather) => {
    const alerts = []
    
    if (weather.temp > 35) {
        alerts.push(`🌡️ Extreme Heat Alert: Temperature is ${Math.round(weather.temp)}°C`)
    } else if (weather.temp < 10) {
        alerts.push(`❄️ Cold Weather Alert: Temperature is ${Math.round(weather.temp)}°C`)
    }
    
    if (weather.windSpeed > 10) {
        alerts.push(`💨 Wind Advisory: Strong winds of ${Math.round(weather.windSpeed * 3.6)} km/h detected`)
    }
    
    if (weather.humidity > 80) {
        alerts.push(`💧 High Humidity Alert: ${weather.humidity}% humidity may cause discomfort`)
    }
    
    switch (weather.condition.toLowerCase()) {
        case 'thunderstorm':
            alerts.push('⛈️ Severe Weather Alert: Thunderstorms detected')
            break
        case 'rain':
            alerts.push('🌧️ Rain Alert: Precipitation expected')
            break
        case 'snow':
            alerts.push('❄️ Snow Alert: Snowy conditions')
            break
    }
    
    return alerts
}

const addMarker = (coordinates, title) => {
    if (!map) return

    // Clear existing markers
    if (markerLayer) {
        map.removeLayer(markerLayer)
    }

    const marker = new Feature({
        geometry: new Point(fromLonLat([coordinates.lon, coordinates.lat]))
    })

    const markerStyle = new Style({
        image: new Circle({
            radius: 6,
            fill: new Fill({ color: '#ff0000' }),
            stroke: new Stroke({
                color: '#ffffff',
                width: 2
            })
        })
    })

    marker.setStyle(markerStyle)

    markerLayer = new VectorLayer({
        source: new VectorSource({
            features: [marker]
        })
    })

    map.addLayer(markerLayer)
}

// Initialize on mount
onMounted(() => {
    try {
        initializeMap()
        startMonitoring()
        
        // Check for existing weather data with storage availability check
        if (typeof window !== 'undefined' && window.localStorage) {
            const savedWeatherAlert = localStorage.getItem('currentWeatherAlert')
            if (savedWeatherAlert) {
                const alertData = JSON.parse(savedWeatherAlert)
                fetchWeatherData(alertData.coordinates.lat, alertData.coordinates.lon)
            }
        }
        
        // Set up monitoring interval
        monitoringInterval.value = setInterval(monitorWeatherSearches, 2000)
        
        // Check if user is admin
        if (typeof window !== 'undefined' && window.localStorage) {
            isAdmin.value = localStorage.getItem('isAdmin') === 'true'
        }
    } catch (error) {
        console.error('Error in onMounted:', error)
        // Set default values if storage access fails
        isAdmin.value = false
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.user')) {
            isDropdownOpen.value = false
        }
    })
})

// Cleanup on unmount
onUnmounted(() => {
    // Clear the interval if it exists
    if (monitoringInterval.value) {
        clearInterval(monitoringInterval.value)
    }
    
    // Clean up map
    if (map) {
        map.setTarget(null)
    }
})

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
/* Update these styles to match the HTML version */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(180deg, #4a6fc0 0%, #8aa4e3 100%);
    color: rgb(0, 0, 0);
    min-height: 100vh;
}

.navbar {
    background-color: rgba(0, 0, 0, 0.7);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 24px;
    text-decoration: none;
}

.nav-links {
    display: flex;
    gap: 20px;
}

.nav-link {
    color: white;
    text-decoration: none;
}

.content-container {
    display: grid;
    grid-template-columns: 400px 600px;
    gap: 100px;
    justify-content: start;
    padding: 30px 60px;
    margin-top: 20px;
    margin-left: 50px;
}

.left-column {
    display: flex;
    flex-direction: column;
    gap: 30px;
    width: 100%;
}

.alert-box {
    background-color: white;
    padding: 25px;
    border-radius: 10px;
    color: black;
    width: 100%;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.weather-map {
    padding: 25px;
    border-radius: 10px;
    width: 1025px;
}

.weather-details {
    background-color: white;
    padding: 25px;
    border-radius: 10px;
    color: black;
    width: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.map-container {
    background: rgba(255, 255, 255, 0.2);
    padding: 20px;
    border-radius: 10px;
}

#weatherMapContainer {
    height: 500px;
    width: 930px;
    border-radius: 5px;
    overflow: hidden;
}

.alert-icon {
    color: #ffd700;
}

h3 {
    margin-bottom: 20px;
    color: black;
}

p {
    margin-bottom: 15px;
    line-height: 1.5;
}

.marker-popup {
    position: absolute;
    background: white;
    padding: 5px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    font-size: 12px;
    pointer-events: none;
    z-index: 1000;
}

.marker-popup div {
    line-height: 1.4;
}

/* Dropdown styles */
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
</style>