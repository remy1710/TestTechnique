<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const router = useRouter()

const handleSubmit = async () => {
    try {
        const response = await axios.post('http://localhost:8000/api/login', {
            email: email.value,
            pass: password.value
        })
        
        if (response.data.user) {
            console.log(response.data.user)
            localStorage.setItem('user', JSON.stringify(response.data.user))
            router.push({ name: 'Home' })        
        }
    } catch (error) {
        console.log(error)
        let message = error.response?.data?.message || 'Une erreur est survenue, API Injoignable ?'
        alert(message)
    }
}
</script>

<template>
  <div class="form">
    <h1 class="title">Connectez-vous</h1>
    <form @submit.prevent="handleSubmit">
      <input type="text" class="input" v-model="email" placeholder="Email" />
      <input type="password" class="input" v-model="password" placeholder="Mot de passe" />
      <button class="button" type="submit">Se connecter</button>
    </form>
  </div>
</template>

<style scoped>
* {
  box-sizing: border-box;
}

.form {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background-color: #fff;
	padding: 20px;
	border-radius: 10px;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.title {
	text-align: center;
    font-weight: bold;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.input {
	width: 100%;
	height: 35px;
	border: 1px solid #ccc;
	border-radius: 5px;
	padding: 5px;
	margin-bottom: 10px;
}
.button {
	width: 100%;
	height: 35px;
	border: none;
	border-radius: 5px;
	background-color: #4CAF50;
	color: white;  
  box-sizing: border-box;
}
</style>
