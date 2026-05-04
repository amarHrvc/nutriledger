import VisitsView from '../../../../views/visits/index';

export default function Page() {
  // Server component entry for Dashboard Visits page — delegates to client VisitsView
  return (
    <div>
      <VisitsView />
    </div>
  );
}
