import Box from '@mui/material/Box'
import Typography from '@mui/material/Typography'


interface InfoRowProps {
  label: string,
  value: string | null | undefined
}

export default function InfoRow(props: InfoRowProps) {
  return (
    <Box className='flex flex-col gap-0.5 py-1'>
      <Typography variant='caption' color='text.secondary'>
        {props.label}
      </Typography>

      <Typography variant='caption' color='text.secondary'>
        {props.label}
      </Typography>
    </Box>
  )
}
