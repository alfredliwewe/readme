const { useState, useEffect,createContext,useContext } = React;
const {TextField, Button, Fab, Link, Typography, InputAdornment, Alert, Tabs, Tab} = MaterialUI;
const {Box, Drawer, List, Divider, ListItem, ListItemButton, ListItemIcon, ListItemText, SvgIcon, createTheme, ThemeProvider} = MaterialUI;
const {Dialog, DialogActions,DialogContent, DialogContentText, MenuItem, DialogTitle} = MaterialUI;
let {alpha, TableBody, TableCell, TableContainer, RadioGroup, Radio, FormLabel,Rating, Table,
    TableHead, TablePagination, TableRow, TableSortLabel, Toolbar, Paper, Checkbox, IconButton, Tooltip,
    Chip, Avatar, FilledInput, FormControl, InputLabel, Breadcrumbs, Input, ListItemAvatar, 
    FormControlLabel,Switch, DeleteIcon, FilterListIcon, visuallyHidden,Menu,
} = MaterialUI;

const {red,blue,grey} = MaterialUI.colors;

let theme = createTheme({
    palette: {
        primary: {
            main: '#0052cc',
        },
        secondary: {
            main: '#edf2ff',
        },
    },
    components:{
        MuiButton:{
            styleOverrides:{
                root:{
                    borderRadius:"64px",
                    textTransform:"none",
                    padding:"6px 24px"
                }
            },
            defaultProps:{
                variant:"contained"
            }
        },
        MuiDialog:{
            styleOverrides:{
                root:{
                    borderRadius:"24px",
                    textTransform:"none"
                },
                paper:{
                    borderRadius:"16px"
                }
            }
        },
        MuiOutlinedInput: {
            styleOverrides: {
                root: {
                    '& .MuiOutlinedInput-notchedOutline': {
                        borderRadius: '8px',
                    },
                },
            },
        },
    }
});

const styles = {
    fab:{
        boxShadow:"none",
        background:"inherit"
    },
    btn:{
        textTransform:"none",
        lineHeight:"unset"
    },
    smallBtn:{
        textTransform:"none",
        lineHeight:"unset",
        padding:"5px 14px"
    }
}

window.onload = function(){
    ReactDOM.render(<ThemeProvider theme={theme}><Welcome /></ThemeProvider>, document.getElementById("root"));
}

var Context = createContext({});
var url = "cms/api.php";
var format = new Intl.NumberFormat();

function Welcome(){
    const [page,setPage] = useState("Home");

    const [menus,setMenus] = useState([
        {
            title:"Home",
            icon:"fa fa-home"
        },
        /*{
            title:"Users",
            icon:"fa fa-list-ol"
        },*/
        {
            title:"Menus",
            icon:"fa fa-list-ol"
        },
        {
            title:"Profile",
            icon:"fa fa-user-friends text-danger"
        },
    ]);

    return (
        <>
            <div className="w3-row">
                <div className="w3-col m2 w3-border-right" style={{height:innerHeight+"px",overflowY:"auto"}}>
                    {menus.map((row,index)=>(
                        <MenuButton data={row} key={row.title} isActive={page == row.title} onClick={()=>setPage(row.title)} />
                    ))}

                    <MenuButton data={{
                        title:"Logout",
                        icon:"fa fa-power-off"
                    }} isActive={false} onClick={()=>{
                        window.location = '../logout.php';
                    }} />
                </div>
                <div className="w3-col m10" style={{height:innerHeight+"px",overflowY:"auto"}}>
                    {page == "Menus" ? <Menus2 />:
                    <>{page}</>}
                </div>
            </div>
        </>
    )
}

function MenuButton(props){
    return (
        <>
            <div className={"p-2 hover:bg-gray-100 pointer menu-button "+(props.isActive?"bg-gray-100 active":"")} onClick={e=>{
                if(props.onClick != undefined){
                    props.onClick(e);
                }
            }}>
                <span className="w3-center" style={{width:"50px",display:"inline-block"}}>
                    <i className={props.data.icon} />
                </span>
                <font>{props.data.title}</font>
            </div>
        </>
    )
}

function Menus2(){
    const [rows,setRows] = useState([]);
    const [open,setOpen] = useState({
        add:false
    });
    const [active,setActive] = useState({});
    const [form,setForm] = useState({
        title:"",
    })

    const getRows = () => {
        $.get("api/", {getMenus:"true"}, res=>setRows(res));
    }

    const saveMenu = (event) => {
        event.preventDefault();

        $.post("api/", $(event.target).serialize(), response=>{
            try{
                let res = JSON.parse(response);
                if(res.status){
                    setOpen({...open, add:false});
                    getRows();
                    Toast("Success");
                }
                else{
                    Toast(res.message);
                }
            }
            catch(E){
                alert(E.toString()+response);
            }
        })
    }

    useEffect(()=>{
        getRows();
    }, []);

    return (
        <>
            <div className="p-2">
                <Button onClick={e=>setOpen({...open, add:true})}>Add Menu</Button>

                <Table sx={{mt:2}}>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableCell>#</TableCell>
                                <TableCell>Name</TableCell>
                                <TableCell>Type</TableCell>
                                <TableCell>Md File</TableCell>
                                <TableCell>Html File</TableCell>
                                <TableCell>Parent</TableCell>
                                <TableCell>Action</TableCell>
                            </TableRow>
                        </TableHead>
                    </Table>
                </Table>
            </div>

            <Dialog open={open.add} onClose={()=>setOpen({...open, add:false})}>
                <div className="p-3" style={{width:"400px"}}>
                    <CloseHeading label="Add Menu" onClose={()=>setOpen({...open, add:false})}/>

                    <form onSubmit={saveMenu}>
                        <TextField label="Parent" size="small" sx={{mt:2}} fullWidth defaultValue={0} name="parent" select>
                            <MenuItem value={0}>{"No parent"}</MenuItem>
                            {rows
                            .filter(r=>r.type =="category")
                            .map((row,index)=>(
                                <MenuItem value={row.id}>{row.name}</MenuItem>
                            ))}
                        </TextField>
                        <TextField 
                            label="Title/Name" 
                            size="small" 
                            sx={{mt:2}} 
                            fullWidth 
                            value={form.title}
                            onChange={e=>setForm({...form, title:e.target.value})}
                            name="new_menu" />
                        <TextField label="Type" size="small" sx={{mt:2}} fullWidth defaultValue={'notes'} name="type" select>
                            {["notes","category"].map((row,index)=>(
                                <MenuItem value={row}>{row}</MenuItem>
                            ))}
                        </TextField>
                        <TextField 
                            label="Markdown file" 
                            size="small" 
                            sx={{mt:2}} 
                            fullWidth 
                            defaultValue={form.title+".md"}
                            name="md_file" />
                        <TextField label="HTML file" size="small" sx={{mt:2,mb:3}} fullWidth name="html_file" />
                        <Button>Submit</Button>
                    </form>
                </div>
            </Dialog>
        </>
    )
}

function CloseHeading(props){
    return (
        <>
            <div className={"clearfix "+(props.className != undefined ? props.className : "")}>
                <font className="w3-large">{props.label}</font>

                <span className="bg-gray-2001 w3-round-large bcenter float-right pointer hover:bg-gray-300 border" onClick={e=>{
                    if(props.onClose != undefined){
                        props.onClose(e);
                    }
                }} style={{height:"30px",width:"30px"}}>
                    <i className="fa fa-times"/>
                </span>
            </div>
        </>
    )
}

function BottomClose(props){
    return (
        <>
            <div className={"clearfix "+(props.className != undefined ? props.className : "")}>
                <Button variant="contained" color="error" className="float-right" onClick={e=>{
                    if(props.onClose != undefined){
                        props.onClose(e);
                    }
                }} style={{textTransform:"none"}}>
                    Close
                </Button>
            </div>
        </>
    )
}

function Warning(props){
    const [open,setOpen] = useState(true);

    useEffect(()=>{
        if(!open){
            if(props.onClose!= undefined){
                props.onClose();
            }
        }
    }, [open]);

    return (
        <Dialog open={open} onClose={()=>{
            setOpen(false)
            if(props.onClose!= undefined){
                props.onClose();
            }
        }}>
            <div className="w3-padding-large" style={{width:"300px"}}>
                {props.title != undefined && <font className="w3-large block mb-30 block">{props.title}</font>}

                {props.secondaryText != undefined && <font className="block mb-15">{props.secondaryText}</font>}

                {props.view != undefined && <div className="py-2">{props.view}</div>}
                
                <div className="py-2 clearfix">
                    <Button variant="contained" color="error" className="w3-round-xxlarge" sx={{textTransform:"none"}} onClick={event=>{
                        setOpen(false)
                        if(props.onClose!= undefined){
                            props.onClose();
                        }
                    }}>Close</Button>
                    <span className="float-right">
                        
                        {props.action != undefined && <Button sx={{textTransform:"none"}} className="w3-round-xxlarge" variant="contained" onClick={event=>{
                            //setLogout(false);
                            props.action.callback();
                        }}>{props.action.text}</Button>}
                    </span>
                </div>
            </div>
        </Dialog>
    )
}

function a11yProps(index) {
    return {
        id: `simple-tab-${index}`,
        'aria-controls': `simple-tabpanel-${index}`,
    };
}

function TabPanel(props) {
    const {children, value, index, ...other} = props;

    return (
        <div role="tabpanel" hidden={value !== index} id={`simple-tabpanel-${index}`} aria-labelledby={`simple-tab-${index}`} {...other}>
            {value === index && (
                <Box>
                    {children}
                </Box>
            )}
        </div>
    );
}

function DropSelect(props) {
    const [anchorEl, setAnchorEl] = React.useState(null);
    const [data,setData] = useState([]);
    const open = Boolean(anchorEl);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    useEffect(()=>{
        setData(props.data.map(r=>{
            //r.checked = false;
            return r;
        }));
    }, [props.data]);

    useEffect(()=>{
        if(props.onChange != undefined){
            props.onChange(data.filter(r=>r.checked))
        }
    }, [data]);
  
    return (
        <>
        <Button
            id="basic-button"
            aria-controls={open ? 'basic-menu' : undefined}
            aria-haspopup="true"
            variant="outlined"
            aria-expanded={open ? 'true' : undefined}
            sx={{...styles.smallBtn, mx:2}}
            onClick={handleClick}>
            {props.label}
        </Button>
        <Menu
            id="basic-menu"
            anchorEl={anchorEl}
            open={open}
            onClose={handleClose}
            sx={{minWidth:"120px",p:1}}
            MenuListProps={{
                'aria-labelledby': 'basic-button',
            }}
        >
            <div className="p-2" style={{minWidth:"150px"}}>
                {data.map((row,index)=>(
                    <div className="p-1 hover:bg-gray-200 pointer rounded" key={row.id}>
                        <input 
                            type="checkbox" 
                            checked={row.checked}
                            onChange={e=>{
                                //Toast("id is "+row.id)
                                setData(data.map((r,i)=>{
                                    if(i == index){
                                        r.checked = e.target.checked;
                                    }
                                    return r;
                                }))
                            }}
                            id={"check_"+row.id}/> 
                        <label htmlFor={"check_"+row.id} className="pl-2">{row.name}</label>
                    </div>
                ))}
            </div>
        </Menu>
        </>
    );
}