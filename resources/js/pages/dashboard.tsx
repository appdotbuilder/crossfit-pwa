import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';

interface Props {
    user: {
        name: string;
        email: string;
        role: string;
        membership_type?: string;
        membership_expires_at?: string;
        has_active_membership: boolean;
    };
    upcoming_bookings: Array<{
        id: number;
        status: string;
        booking_type: string;
        amount_paid?: number;
        class: {
            id: number;
            name: string;
            starts_at: string;
            duration_minutes: number;
            instructor_name: string;
        };
        is_refundable: boolean;
    }>;
    available_classes: Array<{
        id: number;
        name: string;
        description?: string;
        starts_at: string;
        duration_minutes: number;
        instructor_name: string;
        available_spots: number;
        max_participants: number;
        teen_approved: boolean;
        drop_in_price?: number;
        is_full: boolean;
    }>;
    [key: string]: unknown;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard({ user, upcoming_bookings, available_classes }: Props) {
    const formatTime = (dateString: string) => {
        return new Date(dateString).toLocaleString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        });
    };

    const handleBookClass = (classId: number) => {
        router.post(route('bookings.store'), {
            class_id: classId,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleCancelBooking = (bookingId: number) => {
        if (confirm('Are you sure you want to cancel this booking?')) {
            router.delete(route('bookings.destroy', bookingId), {
                preserveState: true,
                preserveScroll: true,
            });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            
            <div className="space-y-8">
                {/* Welcome Header */}
                <div className="bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl p-8 text-white">
                    <h1 className="text-3xl font-bold mb-2">Welcome back, {user.name}! 💪</h1>
                    <p className="text-red-100 text-lg">
                        Ready to crush your fitness goals today?
                    </p>
                    
                    {/* Membership Status */}
                    <div className="mt-4 bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-red-100">Membership Status</p>
                                <p className="font-semibold">
                                    {user.membership_type ? (
                                        <span className="capitalize">{user.membership_type.replace('_', ' ')}</span>
                                    ) : (
                                        'No Active Membership'
                                    )}
                                </p>
                            </div>
                            <div className="text-right">
                                {user.has_active_membership ? (
                                    <span className="bg-green-500 text-white px-3 py-1 rounded-full text-sm">
                                        ✅ Active
                                    </span>
                                ) : (
                                    <span className="bg-orange-500 text-white px-3 py-1 rounded-full text-sm">
                                        ⚠️ Inactive
                                    </span>
                                )}
                            </div>
                        </div>
                        {user.membership_expires_at && (
                            <p className="text-sm text-red-100 mt-2">
                                Expires: {new Date(user.membership_expires_at).toLocaleDateString()}
                            </p>
                        )}
                    </div>
                </div>

                {/* Stats Cards */}
                <div className="grid md:grid-cols-3 gap-6">
                    <div className="bg-white rounded-xl p-6 shadow-sm border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-gray-600 text-sm">Upcoming Classes</p>
                                <p className="text-2xl font-bold text-gray-900">{upcoming_bookings.length}</p>
                            </div>
                            <div className="text-3xl">📅</div>
                        </div>
                    </div>
                    
                    <div className="bg-white rounded-xl p-6 shadow-sm border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-gray-600 text-sm">Available Classes</p>
                                <p className="text-2xl font-bold text-gray-900">{available_classes.length}</p>
                            </div>
                            <div className="text-3xl">🏋️</div>
                        </div>
                    </div>
                    
                    <div className="bg-white rounded-xl p-6 shadow-sm border">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-gray-600 text-sm">Member Level</p>
                                <p className="text-2xl font-bold text-gray-900 capitalize">{user.role}</p>
                            </div>
                            <div className="text-3xl">⭐</div>
                        </div>
                    </div>
                </div>

                {/* My Upcoming Classes */}
                <div className="bg-white rounded-xl shadow-sm border">
                    <div className="p-6 border-b">
                        <h2 className="text-xl font-bold text-gray-900">🔥 My Upcoming Classes</h2>
                    </div>
                    
                    <div className="p-6">
                        {upcoming_bookings.length > 0 ? (
                            <div className="space-y-4">
                                {upcoming_bookings.map((booking) => (
                                    <div key={booking.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div className="flex-1">
                                            <h3 className="font-semibold text-gray-900">{booking.class.name}</h3>
                                            <p className="text-sm text-gray-600">
                                                📍 {formatTime(booking.class.starts_at)} • {booking.class.instructor_name}
                                            </p>
                                            <div className="flex items-center gap-4 mt-2">
                                                <span className={`text-xs px-2 py-1 rounded-full ${
                                                    booking.status === 'confirmed' 
                                                        ? 'bg-green-100 text-green-800' 
                                                        : 'bg-orange-100 text-orange-800'
                                                }`}>
                                                    {booking.status === 'confirmed' ? '✅ Confirmed' : '⏳ Waiting List'}
                                                </span>
                                                {booking.amount_paid && (
                                                    <span className="text-xs text-gray-500">
                                                        Paid: ${booking.amount_paid}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                        
                                        {booking.is_refundable && (
                                            <button
                                                onClick={() => handleCancelBooking(booking.id)}
                                                className="text-red-600 hover:text-red-700 text-sm font-medium"
                                            >
                                                Cancel
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-gray-500">
                                <div className="text-4xl mb-4">🏃‍♂️</div>
                                <p>No upcoming classes booked.</p>
                                <p className="text-sm">Book a class below to get started!</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Available Classes */}
                <div className="bg-white rounded-xl shadow-sm border">
                    <div className="p-6 border-b">
                        <h2 className="text-xl font-bold text-gray-900">🚀 Book a Class</h2>
                    </div>
                    
                    <div className="p-6">
                        {available_classes.length > 0 ? (
                            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {available_classes.map((classItem) => (
                                    <div key={classItem.id} className="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div className="flex items-start justify-between mb-3">
                                            <h3 className="font-bold text-gray-900">{classItem.name}</h3>
                                            {classItem.teen_approved && (
                                                <span className="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                    Teen OK
                                                </span>
                                            )}
                                        </div>
                                        
                                        <p className="text-sm text-gray-600 mb-2">
                                            📍 {formatTime(classItem.starts_at)}
                                        </p>
                                        
                                        <p className="text-sm text-gray-600 mb-3">
                                            👨‍🏫 {classItem.instructor_name}
                                        </p>
                                        
                                        {classItem.description && (
                                            <p className="text-sm text-gray-500 mb-3 line-clamp-2">
                                                {classItem.description}
                                            </p>
                                        )}
                                        
                                        <div className="flex items-center justify-between mb-4">
                                            <span className="text-sm">
                                                {classItem.available_spots > 0 ? (
                                                    <span className="text-green-600 font-medium">
                                                        ✅ {classItem.available_spots} spots left
                                                    </span>
                                                ) : (
                                                    <span className="text-orange-600 font-medium">
                                                        ⏳ Join waiting list
                                                    </span>
                                                )}
                                            </span>
                                            
                                            {classItem.drop_in_price && (
                                                <span className="text-sm font-medium text-gray-700">
                                                    ${classItem.drop_in_price}
                                                </span>
                                            )}
                                        </div>
                                        
                                        <button
                                            onClick={() => handleBookClass(classItem.id)}
                                            className="w-full bg-red-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-red-700 transition-colors"
                                        >
                                            {classItem.is_full ? 'Join Waiting List' : 'Book Class'}
                                        </button>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-gray-500">
                                <div className="text-4xl mb-4">📅</div>
                                <p>No classes available to book right now.</p>
                                <p className="text-sm">Check back later for new classes!</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}