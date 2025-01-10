// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyD3ky26k4QwLlWPDmprsUkxQQWxHNL6Doo",
  authDomain: "penjadwalansidang-2d73d.firebaseapp.com",
  projectId: "penjadwalansidang-2d73d",
  storageBucket: "penjadwalansidang-2d73d.appspot.com",
  messagingSenderId: "862886418965",
  appId: "1:862886418965:web:f07b73f329aa03417d0c1c",
  measurementId: "G-TN691L4V5L"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);