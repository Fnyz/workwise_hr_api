import React, { useEffect, useRef } from 'react'
import { useAuth } from '../context';
import axiosClient from '../axiosClient';
import 'boxicons'
import { useGoogleLogin } from '@react-oauth/google';
import axios from 'axios';
function Login() {

  const {setUser, setToken} =  useAuth();
  const email = useRef();
  const password = useRef();


  const loginWithGoogle =  useGoogleLogin({
        onSuccess: tokenResponse => {
                axios.get('https://www.googleapis.com/oauth2/v1/userinfo', {
        headers: {
            Authorization: `Bearer ${tokenResponse.access_token}`
        }
        })
        .then(userInfoResponse => {
            onGoogleSubmit(userInfoResponse.data)
        })
        .catch(error => {
          console.error('Error fetching user data from Google:', error);
        });
        },
  });

  const onGoogleSubmit = (data) => {
    
    const payload = {
        name: data.name,
        email: data.email,
        image: data.picture,
        provider: 'GOOGLE',
    }
 
    axiosClient.post("/register", payload)
   .then(({data}) => {
       setUser(data.user);
       setToken(data.token);
      })
   .catch((err) => {
       const {response} = err;
       if(response &&  response.status  === 422){
           Object.keys(response.data.errors).map((err)=>{
            console.log(err);
           })
       }
    });
  }

  
  const login = (ev) => {
    ev.preventDefault();

    const payload = {
      email: email.current.value,
      password: password.current.value,
      provider: 'LOCAL',
    }

    axiosClient.post('/login', payload)
    .then(({data})=>{
       setToken(data.token)
       setUser(data.user);
    }).catch((err)=>{
        const {response} = err;
        if (response && response.status === 422) {
            console.log(response.data.errors);
            localStorage.removeItem('ACCESS_TOKEN');
        }
    })
  }


  

  return (
    <>
            
        <div className="flex min-h-screen items-center justify-center">
        <div className="relative flex flex-col rounded-xl bg-transparent bg-clip-border text-gray-700 shadow-none">
            <h4 className="block font-sans text-2xl font-semibold leading-snug tracking-normal text-blue-gray-900 antialiased">
            Login for Workwise<span className=' text-[#00b894] font-bold'>HR</span>
            </h4>
            <p className="mt-1 block font-sans text-base font-normal leading-relaxed text-gray-700 antialiased">
            Input all the fields
            </p>
  
            <form onSubmit={login} className="mt-8 mb-2 w-80 max-w-screen-lg sm:w-96">
            <div className="mb-4 flex flex-col gap-6">
                
                <div className="relative h-11 w-full min-w-[200px]">
                <input
                    ref={email}
                    className="peer h-full w-full rounded-md border border-blue-gray-200 bg-transparent px-3 py-3 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-[#00b894] focus:border-t-transparent focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50"
                    placeholder=" "
                />
                <label className="before:content[' '] after:content[' '] pointer-events-none absolute left-0 -top-1.5 flex h-full w-full select-none text-[11px] font-normal leading-tight text-blue-gray-400 transition-all before:pointer-events-none before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5 before:w-2.5 before:rounded-tl-md before:border-t before:border-l before:border-blue-gray-200 before:transition-all after:pointer-events-none after:mt-[6.5px] after:ml-1 after:box-border after:block after:h-1.5 after:w-2.5 after:flex-grow after:rounded-tr-md after:border-t after:border-r after:border-blue-gray-200 after:transition-all peer-placeholder-shown:text-sm peer-placeholder-shown:leading-[4.1] peer-placeholder-shown:text-blue-gray-500 peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent peer-focus:text-[11px] peer-focus:leading-tight peer-focus:text-[#00b894] peer-focus:before:border-t-2 peer-focus:before:border-l-2 peer-focus:before:!border-[#00b894] peer-focus:after:border-t-2 peer-focus:after:border-r-2 peer-focus:after:!border-[#00b894] peer-disabled:text-transparent peer-disabled:before:border-transparent peer-disabled:after:border-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500">
                    Email
                </label>
                </div>
                <div className="relative h-11 w-full min-w-[200px]">
                <input
                    ref={password}
                    type="password"
                    className="peer h-full w-full rounded-md border border-blue-gray-200 bg-transparent px-3 py-3 font-sans text-sm font-normal text-blue-gray-700 outline outline-0 transition-all placeholder-shown:border placeholder-shown:border-blue-gray-200 placeholder-shown:border-t-blue-gray-200 focus:border-2 focus:border-[#00b894] focus:border-t-transparent focus:outline-0 disabled:border-0 disabled:bg-blue-gray-50"
                    placeholder=" "
                />
                <label className="before:content[' '] after:content[' '] pointer-events-none absolute left-0 -top-1.5 flex h-full w-full select-none text-[11px] font-normal leading-tight text-blue-gray-400 transition-all before:pointer-events-none before:mt-[6.5px] before:mr-1 before:box-border before:block before:h-1.5 before:w-2.5 before:rounded-tl-md before:border-t before:border-l before:border-blue-gray-200 before:transition-all after:pointer-events-none after:mt-[6.5px] after:ml-1 after:box-border after:block after:h-1.5 after:w-2.5 after:flex-grow after:rounded-tr-md after:border-t after:border-r after:border-blue-gray-200 after:transition-all peer-placeholder-shown:text-sm peer-placeholder-shown:leading-[4.1] peer-placeholder-shown:text-blue-gray-500 peer-placeholder-shown:before:border-transparent peer-placeholder-shown:after:border-transparent peer-focus:text-[11px] peer-focus:leading-tight peer-focus:text-[#00b894] peer-focus:before:border-t-2 peer-focus:before:border-l-2 peer-focus:before:!border-[#00b894] peer-focus:after:border-t-2 peer-focus:after:border-r-2 peer-focus:after:!border-[#00b894] peer-disabled:text-transparent peer-disabled:before:border-transparent peer-disabled:after:border-transparent peer-disabled:peer-placeholder-shown:text-blue-gray-500">
                    Password
                </label>
                </div>
            </div>
           
            <button
                className="mt-6 block w-full select-none rounded-lg bg-[#00b894] py-3 px-6 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-pink-500/20 transition-all hover:shadow-lg hover:shadow-[#00b894]/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="submit"
                data-ripple-light="true"
            >
                Login
            </button> 
           
           
            <p className="mt-4 block text-center font-sans text-base font-normal leading-relaxed text-gray-700 antialiased">
                Don't have an account?
                <a
                className="font-semibold text-[#00b894] transition-colors hover:text-blue-700 ml-1"
                href="/register"
                >
                Register
                </a>
            </p>
            </form>

            <div className='w-full  justify-center '>
         

            <button type='submit' className="btn btn-active btn-info my-2 w-full text-white" onClick={() => loginWithGoogle()}><box-icon color="white" size="sm"  type='logo' name='google'></box-icon> Login with Google</button>
           
            </div>

            

            
        
        </div>
        </div>
            
 
    </>
  )
}

export default Login