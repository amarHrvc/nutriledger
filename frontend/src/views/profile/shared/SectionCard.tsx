import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'
import Typography from '@mui/material/Typography'
import Divider from '@mui/material/Divider'
import Box from '@mui/material/Box'

interface SectionCardProps {
  title: string
  children: React.ReactNode
}

export default function SectionCard({ title, children }: SectionCardProps) {
  return (
    <Card>
      <CardContent>
        <Typography variant='subtitle1' fontWeight={600} mb={1}>
          {title}
        </Typography>
        <Divider sx={{ mb: 2 }} />
        <Box className='flex flex-col gap-2'>{children}</Box>
      </CardContent>
    </Card>
  )
}
