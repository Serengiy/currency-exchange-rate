import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from "react";
import axios from "axios";
import { ReactTabulator } from "react-tabulator";

export default function CurrencyShowPage() {
    const [loading, setLoading] = useState(true)
    const [rate, setRate] = useState()
    const searchParams = new URLSearchParams(window.location.search);
    const currency = searchParams.get('currency');
    const [pagination, setPagination] = useState({})
    const columns = [
        { title: "Currency", field: "currency",},
        { title: "Rate", field: "rate", },
        { title: "Updated At", field: "updated_at",},
    ]
    const options = {
        layout: 'fitColumns',
        paginationSize: pagination?.per_page,
        pagination:'remote',
        paginationDataSent: {
            page: 'page',
        },
        paginationDataReceived: {
            data: 'data',
            last_page: 'last_page',
        },
    };

    useEffect(() => {
        fetchData();
        const interval = setInterval(fetchData, 60000);

        return () => clearInterval(interval);
    }, [axios, route]);

    function fetchData(page=1) {
        axios.get(route('rates.show')+ `?currency=${currency}&page=${page}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('apiToken')}`
            }
        })
            .then(response => {
                let data = response.data
                setRate(data.data)
                setPagination({
                    current_page: data.meta.current_page,
                    last_page: data.meta.last_page,
                    per_page: data.meta.per_page,
                })
                console.log(data)
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }

    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <ReactTabulator
                            data={rate}
                            columns={columns}
                            options={options}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
