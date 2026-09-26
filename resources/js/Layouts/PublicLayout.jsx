import Navigation from '@/Components/Public/Navigation';
import Footer from '@/Components/Public/Footer';

export default function PublicLayout({ children, settings }) {
    return (
        <div className="min-h-screen flex flex-col font-sans bg-brand-paper text-brand-navy">
            <Navigation />
            
            <main className="flex-grow">
                {children}
            </main>

            <Footer settings={settings} />
        </div>
    );
}