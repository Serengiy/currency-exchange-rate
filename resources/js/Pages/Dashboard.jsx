import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import {useEffect, useState} from "react";

export default function Dashboard() {
    const [user, setUser] = useState()
    const [loading, setLoading] = useState(true)
    useEffect(() => {
        let token = localStorage.getItem('apiToken')
        if(token){
            axios.post(`/api/me` ,{}, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
                .then(function (response) {
                    setUser(response.data.user)
                    setLoading(false)
                })
                .catch(function () {
                    setLoading(false)
                    window.location.href = '/login'
                });
        }else{
            window.location.href = '/login'
            setLoading(false)
        }
    }, []);
    if(loading){
        return (
            <div className="flex justify-center items-center h-screen">
                <div className="text-center">
                    <h1 className="text-3xl">Loading...</h1>
                </div>
            </div>
        );
    }else{
        return (
            <AuthenticatedLayout
                user={user}
                header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
            >
                <Head title="Dashboard" />

                <div className="py-12">
                    <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6 text-gray-900">You're logged in!</div>
                        </div>
                    </div>
                </div>
            </AuthenticatedLayout>
        );
    }
}
