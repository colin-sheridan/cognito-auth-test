export default function Authenticated({ children }) {

    return (
        <div className="min-h-screen">
            <main>{children}</main>
        </div>
    );
}
