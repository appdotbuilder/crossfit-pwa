import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

interface Props {
    tenant: {
        name: string;
        logo_url?: string;
    };
    upcoming_classes: Array<{
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
    }>;
    [key: string]: unknown;
}

export default function Welcome({ tenant, upcoming_classes }: Props) {
    const { auth } = usePage<SharedData>().props;

    const formatTime = (dateString: string) => {
        return new Date(dateString).toLocaleString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        });
    };

    return (
        <>
            <Head title={`${tenant.name} - CrossFit App`}>
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
            </Head>
            <div className="min-h-screen bg-gradient-to-br from-orange-500 via-red-500 to-pink-600">
                {/* Header */}
                <header className="relative z-10 px-6 py-4">
                    <nav className="flex items-center justify-between max-w-7xl mx-auto">
                        <div className="flex items-center space-x-3">
                            <div className="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                                <span className="text-2xl">💪</span>
                            </div>
                            <div>
                                <h1 className="text-white font-bold text-xl">{tenant.name}</h1>
                                <p className="text-orange-100 text-sm">Your CrossFit Community</p>
                            </div>
                        </div>
                        
                        <div className="flex items-center gap-4">
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="bg-white text-red-600 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 transition-colors"
                                >
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('login')}
                                        className="text-white hover:text-orange-100 font-medium transition-colors"
                                    >
                                        Login
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="bg-white text-red-600 px-6 py-2 rounded-full font-semibold hover:bg-gray-100 transition-colors"
                                    >
                                        Join Now
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </header>

                {/* Hero Section */}
                <div className="relative px-6 py-16">
                    <div className="max-w-7xl mx-auto">
                        <div className="text-center mb-16">
                            <h1 className="text-5xl md:text-7xl font-bold text-white mb-6">
                                🔥 GET FIT.<br />
                                <span className="text-orange-200">GET STRONG.</span><br />
                                <span className="text-pink-200">GET RESULTS.</span>
                            </h1>
                            <p className="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
                                Join our high-energy CrossFit community. Book classes, track your progress, 
                                and achieve your fitness goals with expert instructors.
                            </p>
                            
                            {!auth.user && (
                                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                    <Link
                                        href={route('register')}
                                        className="bg-white text-red-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition-colors shadow-lg"
                                    >
                                        🚀 Start Your Journey
                                    </Link>
                                    <Link
                                        href={route('login')}
                                        className="border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-red-600 transition-colors"
                                    >
                                        📱 Member Login
                                    </Link>
                                </div>
                            )}
                        </div>

                        {/* Features Grid */}
                        <div className="grid md:grid-cols-3 gap-8 mb-16">
                            <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                                <div className="text-4xl mb-4">📅</div>
                                <h3 className="text-xl font-bold mb-2">Easy Booking</h3>
                                <p className="text-orange-100">
                                    Book classes instantly, join waiting lists, and manage your schedule effortlessly.
                                </p>
                            </div>
                            
                            <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                                <div className="text-4xl mb-4">👥</div>
                                <h3 className="text-xl font-bold mb-2">Expert Instructors</h3>
                                <p className="text-orange-100">
                                    Train with certified CrossFit coaches who'll push you to reach your potential.
                                </p>
                            </div>
                            
                            <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-white">
                                <div className="text-4xl mb-4">💳</div>
                                <h3 className="text-xl font-bold mb-2">Flexible Memberships</h3>
                                <p className="text-orange-100">
                                    Choose from various membership types including student discounts and day passes.
                                </p>
                            </div>
                        </div>

                        {/* Upcoming Classes */}
                        {upcoming_classes.length > 0 && (
                            <div className="bg-white/95 backdrop-blur-sm rounded-3xl p-8">
                                <div className="flex items-center justify-between mb-6">
                                    <h2 className="text-3xl font-bold text-gray-900">
                                        🏋️ Upcoming Classes
                                    </h2>
                                    {auth.user && (
                                        <Link
                                            href={route('dashboard')}
                                            className="text-red-600 font-medium hover:text-red-700"
                                        >
                                            View All →
                                        </Link>
                                    )}
                                </div>
                                
                                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    {upcoming_classes.map((classItem) => (
                                        <div key={classItem.id} className="bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                                            <div className="flex items-start justify-between mb-3">
                                                <h3 className="font-bold text-lg text-gray-900">{classItem.name}</h3>
                                                {classItem.teen_approved && (
                                                    <span className="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                        Teen OK
                                                    </span>
                                                )}
                                            </div>
                                            
                                            <p className="text-sm text-gray-600 mb-3">
                                                📍 {formatTime(classItem.starts_at)}
                                            </p>
                                            
                                            <p className="text-sm text-gray-600 mb-3">
                                                👨‍🏫 {classItem.instructor_name}
                                            </p>
                                            
                                            <div className="flex items-center justify-between">
                                                <span className="text-sm text-gray-500">
                                                    {classItem.available_spots > 0 ? (
                                                        <span className="text-green-600 font-medium">
                                                            ✅ {classItem.available_spots} spots left
                                                        </span>
                                                    ) : (
                                                        <span className="text-orange-600 font-medium">
                                                            ⏳ Waiting list
                                                        </span>
                                                    )}
                                                </span>
                                                
                                                {classItem.drop_in_price && (
                                                    <span className="text-sm font-medium text-gray-700">
                                                        ${classItem.drop_in_price}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                
                                {!auth.user && (
                                    <div className="text-center mt-8">
                                        <p className="text-gray-600 mb-4">Ready to join a class?</p>
                                        <Link
                                            href={route('register')}
                                            className="bg-red-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-red-700 transition-colors"
                                        >
                                            Sign Up to Book Classes
                                        </Link>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* Membership Types */}
                        <div className="mt-16 bg-white/95 backdrop-blur-sm rounded-3xl p-8">
                            <h2 className="text-3xl font-bold text-gray-900 text-center mb-8">
                                💪 Membership Options
                            </h2>
                            
                            <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div className="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6">
                                    <div className="text-2xl mb-2">🎓</div>
                                    <h3 className="font-bold text-lg mb-2">Student</h3>
                                    <p className="text-sm text-blue-100">Unlimited classes with student discount</p>
                                </div>
                                
                                <div className="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6">
                                    <div className="text-2xl mb-2">⭐</div>
                                    <h3 className="font-bold text-lg mb-2">Standard</h3>
                                    <p className="text-sm text-green-100">Unlimited access to all classes</p>
                                </div>
                                
                                <div className="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6">
                                    <div className="text-2xl mb-2">👶</div>
                                    <h3 className="font-bold text-lg mb-2">Teen</h3>
                                    <p className="text-sm text-purple-100">1-2 teen-approved classes per week</p>
                                </div>
                                
                                <div className="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-xl p-6">
                                    <div className="text-2xl mb-2">🎯</div>
                                    <h3 className="font-bold text-lg mb-2">Drop-in</h3>
                                    <p className="text-sm text-orange-100">Single class or day pass options</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="px-6 py-8 border-t border-white/20">
                    <div className="max-w-7xl mx-auto text-center">
                        <p className="text-orange-100">
                            Built with ❤️ for the CrossFit community • Powered by{" "}
                            <a 
                                href="https://app.build" 
                                target="_blank" 
                                className="font-medium text-white hover:underline"
                            >
                                app.build
                            </a>
                        </p>
                    </div>
                </footer>
            </div>
        </>
    );
}