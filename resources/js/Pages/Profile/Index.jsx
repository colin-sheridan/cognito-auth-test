import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, Link} from '@inertiajs/react';

export default function Index( ) {
    const user = usePage().props.auth.user;
    const session = usePage().props.session;


    return (
        <AuthenticatedLayout >
            <Head title="Profile" />
            <div className="container">
                {/* <Link
                    href={route('logout')}
                    method="get"
                    as="button"
                    className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Log Out
                </Link> */}
                <div className="row justify-content-center">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header bg-gray-100">See your profile information.</div>

                            <img className="card-img-top" src={"https://secure.willamette.edu/img/people/webui.php/" + user.employee_id + ".jpg"}  alt="employee image" />

                            <div className="card-body">
                                <p>
                                    Your Display Name is: {user.display_name}
                                </p>
                                <p>
                                    Your Given Name is: {user.given_name}
                                </p>
                                <p>
                                    Your Family Name is: {user.family_name}
                                </p>
                                <p>
                                    Your Email is: {user.email}
                                </p>
                                <p>
                                    Your ID Number is: {user.employee_id}
                                </p>
                                <p>
                                    Your Current Access Token is: {session}
                                </p>
                                <p>
                                    Your Current Refresh Token is: {user.refresh_token}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}


