import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from "react";
import axios from "axios";
import { ReactTabulator } from "react-tabulator";

export default function CurrencyIndexPage() {
    const [loading, setLoading] = useState(true)
    const [rates, setRates] = useState()
    const columns = [
        { title: "Currency", field: "currency", formatter: "link", formatterParams:  { urlField: "currencyLink",  }},
        { title: "Rate", field: "rate", },
        { title: "Updated At", field: "updated_at", },
    ]

    useEffect(() => {
        fetchData();
        const interval = setInterval(fetchData, 60000);
        return () => clearInterval(interval);
    }, [axios, route]);

    function fetchData() {
        axios.get(route('rates.index'), {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('apiToken')}`
            }
        })
            .then(response => {
                let data = response.data.map(item => ({
                    ...item,
                    currencyLink: `/currency-rate/show?currency=${item.currency}`,
                }));
                setRates(data)
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            });
    }


    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard"/>
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <ReactTabulator
                            data={rates}
                            columns={columns}
                            layout={"fitColumns"}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
